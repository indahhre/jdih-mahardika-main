<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\ActivityLog;
use App\Services\DocumentDuplicateChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query()
            ->with(['mencabut', 'dicabutDengan', 'mencabutDokumenRelasi', 'dicabutOlehRelasi'])
            ->whereNull('archived_at') // Hanya tampilkan dokumen yang tidak diarsipkan di katalog aktif
            ->when($request->string('q')->trim()->toString(), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")->orWhere('nomor', 'like', "%{$search}%");
            }))
            ->when($request->filled('jenis'), fn ($q) => $q->where('tipe_dokumen', $request->jenis))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        // Sorting
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

        // Ambil opsi dropdown filter (hanya dari dokumen aktif)
        $types = Document::whereNull('archived_at')->select('tipe_dokumen')->distinct()->pluck('tipe_dokumen');
        $years = Document::whereNull('archived_at')->select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('admin.documents.index', compact('documents', 'types', 'years'));
    }

    public function create()
    {
        return view('admin.documents.form', [
            'document' => new Document(),
            'documentOptions' => Document::whereNull('archived_at')->orderByDesc('tahun')->get(['id', 'nomor', 'judul']),
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

        // Jika status Berlaku, pastikan dicabut_dengan_id kosong
        if (($data['status'] ?? '') === 'Berlaku') {
            $data['dicabut_dengan_id'] = null;
            $data['dicabut_dengan_teks'] = null;
        }

        $document = Document::create($data);

        // Sinkronisasi relasi pencabutan: dokumen ini mencabut dokumen lain
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

        // Sinkronisasi jika dokumen ini berstatus Tidak Berlaku dan memilih dicabut_dengan_id
        if (!empty($data['dicabut_dengan_id']) && ($data['status'] ?? '') === 'Tidak Berlaku') {
            $revokerId = $data['dicabut_dengan_id'];

            $document->dicabutOlehRelasi()->syncWithPivotValues([$revokerId], [
                'tipe_relasi' => 'Mencabut'
            ]);

            Document::where('id', $revokerId)->update([
                'mencabut_id' => $document->id,
            ]);
        }

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
            'documentOptions' => Document::where('id', '!=', $document->id)->whereNull('archived_at')->orderByDesc('tahun')->get(['id', 'nomor', 'judul']),
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

        // Jika status Berlaku, kosongkan relasi pencabut
        if (($data['status'] ?? '') === 'Berlaku') {
            $data['dicabut_dengan_id'] = null;
            $data['dicabut_dengan_teks'] = null;
            $document->dicabutOlehRelasi()->detach();
        }

        $document->update($data);

        // Sinkronisasi relasi pencabutan saat update (dokumen ini mencabut dokumen lain)
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

        // Sinkronisasi jika dokumen ini Tidak Berlaku dan memilih dicabut_dengan_id
        if (!empty($data['dicabut_dengan_id']) && ($data['status'] ?? '') === 'Tidak Berlaku') {
            $revokerId = $data['dicabut_dengan_id'];

            $document->dicabutOlehRelasi()->syncWithPivotValues([$revokerId], [
                'tipe_relasi' => 'Mencabut'
            ]);

            Document::where('id', $revokerId)->update([
                'mencabut_id' => $document->id,
            ]);
        }

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
        $document->delete(); // soft delete

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS',
            'description' => "Memindahkan dokumen ke Tempat Sampah: '{$judul}'",
        ]);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil dipindahkan ke Tempat Sampah.');
    }

    // ==================== FITUR ARSIP DOKUMEN ====================

    // Mengarsipkan dokumen
    public function archive($id)
    {
        $document = Document::withoutGlobalScopes()->findOrFail($id);

        $document->update([
            'archived_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'ARSIP',
            'description' => "Mengarsipkan dokumen: '{$document->judul}'",
        ]);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil diarsipkan.');
    }

    // Menampilkan daftar dokumen yang diarsipkan
    public function archivedIndex(Request $request)
    {
        $documents = Document::withTrashed()
            ->where(function ($q) {
                $q->whereNotNull('archived_at')
                  ->orWhereNotNull('deleted_at');
            })
            ->when($request->string('q')->trim()->toString(), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")->orWhere('nomor', 'like', "%{$search}%");
            }))
            ->orderByRaw('COALESCE(archived_at, deleted_at) DESC')
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.archived', compact('documents'));
    }

    // Memulihkan dokumen dari arsip
    public function restoreArchived($id)
    {
        $documentId = (int) $id;

        // 1. Ambil dokumen termasuk yang berstatus soft-deleted
        $document = Document::withTrashed()->findOrFail($documentId);

        // 2. Pulihkan jika berstatus soft-deleted
        if ($document->trashed()) {
            $document->restore();
        }

        // 3. Reset status arsip
        $document->archived_at = null;
        $document->save();

        // 4. Pastikan di database kedua kolom bernilai NULL agar muncul di dokumen aktif
        DB::table('documents')
            ->where('id', $documentId)
            ->update([
                'archived_at' => null,
                'deleted_at' => null,
            ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'PULIH ARSIP',
            'description' => "Memulihkan dokumen ke daftar aktif: '{$document->judul}'",
        ]);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil dipulihkan dan dikembalikan ke daftar aktif.');
    }

    // =============================================================

    // Kelola Tempat Sampah (Soft Delete)
    public function trash(Request $request)
    {
        return $this->archivedIndex($request);
    }

    public function restore(Request $request, $id)
    {
        return $this->restoreArchived($id);
    }

    public function forceDelete(Request $request, $id)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya super admin yang boleh menghapus permanen dokumen.');

        $document = Document::withTrashed()->findOrFail($id);
        $judul = $document->judul;
        
        $document->forceDelete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS PERMANEN',
            'description' => "Menghapus permanen dokumen: '{$judul}'",
        ]);

        return redirect()->back()->with('status', 'Dokumen telah dihapus secara permanen.');
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