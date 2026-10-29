<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::latest()->paginate(10);
        return view('admin.document_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        // Normalisasi ke Title Case sebelum validasi
        $formattedName = ucwords(strtolower(trim($request->name)));
        $request->merge(['name' => $formattedName]);

        $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name',
        ], [
            'name.unique' => 'Jenis dokumen ini sudah ada di dalam sistem.',
        ]);

        $documentType = DocumentType::create([
            'name' => $request->name,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'TAMBAH',
            'description' => "Menambahkan jenis dokumen baru: '{$documentType->name}'",
        ]);

        return redirect()->route('admin.document-types.index')->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documents()->count() > 0) {
            return back()->with('error', 'Jenis dokumen tidak bisa dihapus karena sedang digunakan oleh dokumen aktif.');
        }

        $name = $documentType->name;
        $documentType->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS',
            'description' => "Menghapus jenis dokumen: '{$name}'",
        ]);

        return redirect()->route('admin.document-types.index')->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}