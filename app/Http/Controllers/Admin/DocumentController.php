<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\ActivityLog;
use App\Services\DocumentDuplicateChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query()
            ->when($request->string('q')->trim()->toString(), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")->orWhere('nomor', 'like', "%{$search}%");
            }))
            ->when($request->filled('jenis'), fn ($q) => $q->where('tipe_dokumen', $request->jenis))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        // Pengurutan data (Sorting)
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('tahun', 'asc')->orderBy('id', 'asc');
        } elseif ($sort === 'title_asc') {
            $query->orderBy('judul', 'asc');
        } elseif ($sort === 'title_desc') {
            $query->orderBy('judul', 'desc');
        } else {
            $query->orderByDesc('tahun')->orderByDesc('id');
        }

        $documents = $query->paginate(15)->withQueryString();

        // Ambil data unik untuk opsi filter dropdown di view admin
        $types = Document::select('tipe_dokumen')->distinct()->pluck('tipe_dokumen');
        $years = Document::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('admin.documents.index', compact('documents', 'types', 'years'));
    }

    public function create()
    {
        return view('admin.documents.form', [
            'document' => new Document(),
            'documentOptions' => Document::orderByDesc('tahun')->get(['id', 'nomor', 'judul']),
            'documentTypes' => DocumentType::orderBy('name', 'asc')->get(),
        ]);
    }

    public function checkDuplicate(Request $request, DocumentDuplicateChecker $checker)
    {
        $result = $checker->check([
            'file' => $request->file('file_dokumen'),
            'nomor' => $request->input('nomor'),
        ], $request->input('document_id'));

        return response()->json($result);
    }

    public function store(Request $request, DocumentDuplicateChecker $checker)
    {
        $duplicateCheck = $checker->check([
            'file' => $request->file('file_dokumen'),
            'nomor' => $request->input('nomor'),
        ]);

        if ($duplicateCheck['is_blocked']) {
            throw ValidationException::withMessages([
                'file_dokumen' => $duplicateCheck['message'],
            ]);
        }

        $data = $this->validated($request);

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $hash = hash_file('sha256', $file->getRealPath());
            $path = $file->store('documents', 'public');

            $data['file_url'] = Storage::url($path);
            $data['file_hash'] = $hash;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $data['created_by_id'] = $request->user()->id;

        $document = Document::create($data);

        // OTOMATISASI: Jika ada dokumen lama yang dipilih untuk dicabut via mencabut_id
        if (!empty($data['mencabut_id'])) {
            $targetId = $data['mencabut_id'];

            $document->mencabutDokumenRelasi()->syncWithPivotValues([$targetId], [
                'tipe_relasi' => 'Mencabut'
            ]);

            Document::where('id', $targetId)->update([
                'status' => 'Tidak Berlaku',
                'dicabut_dengan_id' => $document->id,
            ]);
        }

        // TAMBAHAN: Catat log aktivitas tambah
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'TAMBAH',
            'description' => "Menambahkan dokumen baru: '{$document->judul}' (Nomor: {$document->nomor})",
        ]);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(Document $document)
    {
        return view('admin.documents.form', [
            'document' => $document,
            'documentOptions' => Document::where('id', '!=', $document->id)->orderByDesc('tahun')->get(['id', 'nomor', 'judul']),
            'documentTypes' => DocumentType::orderBy('name', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Document $document, DocumentDuplicateChecker $checker)
    {
        $duplicateCheck = $checker->check([
            'file' => $request->file('file_dokumen'),
            'nomor' => $request->input('nomor'),
        ], $document->id);

        if ($duplicateCheck['is_blocked']) {
            throw ValidationException::withMessages([
                'file_dokumen' => $duplicateCheck['message'],
            ]);
        }

        $data = $this->validated($request, $document);

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $hash = hash_file('sha256', $file->getRealPath());
            $path = $file->store('documents', 'public');

            $data['file_url'] = Storage::url($path);
            $data['file_hash'] = $hash;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);

        // OTOMATISASI UPDATE: Sinkronisasi relasi via mencabut_id
        if (!empty($data['mencabut_id'])) {
            $targetId = $data['mencabut_id'];

            $document->mencabutDokumenRelasi()->syncWithPivotValues([$targetId], [
                'tipe_relasi' => 'Mencabut'
            ]);

            Document::where('id', $targetId)->update([
                'status' => 'Tidak Berlaku',
                'dicabut_dengan_id' => $document->id,
            ]);
        } else {
            $document->mencabutDokumenRelasi()->detach();
        }

        // TAMBAHAN: Catat log aktivitas ubah
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UBAH',
            'description' => "Memperbarui data dokumen: '{$document->judul}'",
        ]);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Request $request, Document $document)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya super admin yang boleh menghapus dokumen.');

        $judul = $document->judul;
        $document->delete(); // Soft Delete (masuk ke tempat sampah)

        // TAMBAHAN: Catat log aktivitas hapus sementara
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS',
            'description' => "Memindahkan dokumen ke Tempat Sampah: '{$judul}'",
        ]);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil dipindahkan ke Tempat Sampah.');
    }

    // TAMBAHAN BARU: Method untuk Kelola Tempat Sampah (Soft Delete)
    public function trash(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya super admin yang boleh mengakses tempat sampah.');

        $documents = Document::onlyTrashed()->latest()->paginate(15);
        return view('admin.documents.trash', compact('documents'));
    }

    public function restore(Request $request, $id)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya super admin yang boleh memulihkan dokumen.');

        $document = Document::onlyTrashed()->findOrFail($id);
        $document->restore();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'PULIHKAN',
            'description' => "Memulihkan dokumen dari Tempat Sampah: '{$document->judul}'",
        ]);

        return redirect()->route('admin.documents.trash')->with('status', 'Dokumen berhasil dipulihkan.');
    }

    public function forceDelete(Request $request, $id)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya super admin yang boleh menghapus permanen dokumen.');

        $document = Document::onlyTrashed()->findOrFail($id);
        $judul = $document->judul;
        
        $document->forceDelete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS PERMANEN',
            'description' => "Menghapus permanen dokumen: '{$judul}'",
        ]);

        return redirect()->route('admin.documents.trash')->with('status', 'Dokumen telah dihapus secara permanen.');
    }

    private function validated(Request $request, ?Document $document = null): array
    {
        $data = $request->validate([
            'tipe_dokumen' => ['required', 'string', 'exists:document_types,name'],
            'judul' => ['required', 'string'],
            'nomor' => ['required', 'string', 'max:150'],
            'tahun' => ['required', 'integer', 'min:1900', 'max:2100'],
            'status' => ['required', 'in:Berlaku,Tidak Berlaku'],
            'tanggal_penetapan' => ['nullable', 'date'],
            'tanggal_pengundangan' => ['nullable', 'date'],
            'tanggal_berakhir' => ['nullable', 'date'],
            'tempat_penetapan' => ['nullable', 'string', 'max:150'],
            'penandatangan' => ['nullable', 'string', 'max:150'],
            'sumber' => ['nullable', 'string', 'max:150'],
            'subjek' => ['nullable', 'string', 'max:150'],
            'bidang' => ['nullable', 'string', 'max:150'],
            'ringkasan' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'mencabut_id' => ['nullable', 'exists:documents,id'],
            'dicabut_dengan_id' => ['nullable', 'exists:documents,id'],
            'gdrive_url' => ['nullable', 'url'],
            'file_dokumen' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        if (!empty($data['judul'])) {
            $judulInput = trim($data['judul']);
            $kataKecil = ['dan', 'atau', 'tentang', 'di', 'ke', 'dari', 'yang', 'untuk', 'pada'];
            $words = explode(' ', $judulInput);
            
            foreach ($words as $index => $word) {
                $lowerWord = mb_strtolower($word);
                
                if ($index > 0 && in_array($lowerWord, $kataKecil)) {
                    $words[$index] = $lowerWord;
                } elseif ($word === mb_strtoupper($word) && mb_strlen($word) > 1) {
                    $words[$index] = $word;
                } else {
                    $words[$index] = Str::title($lowerWord);
                }
            }
            $data['judul'] = implode(' ', $words);
        }

        if (!empty($data['tags'])) {
            $tagsArray = array_map('trim', explode(',', $data['tags']));
            $tagsArray = array_map('mb_strtolower', array_filter($tagsArray));
            $data['tags'] = array_values(array_unique($tagsArray));
        } else {
            $data['tags'] = [];
        }

        if ($data['status'] === 'Berlaku') {
            $data['dicabut_dengan_id'] = null;
        }

        return $data;
    }
}