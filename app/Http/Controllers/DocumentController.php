<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query();

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('nomor', 'like', "%{$search}%")
                    ->orWhere('subjek', 'like', "%{$search}%");
            });
        }

        if ($type = $request->string('jenis')->trim()->toString()) {
            $query->where('tipe_dokumen', $type);
        }

        if ($year = $request->string('tahun')->trim()->toString()) {
            $query->where('tahun', $year);
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $documents = $query->orderByDesc('tahun')->orderByDesc('id')->paginate(12)->withQueryString();

        // Diurutkan berdasarkan alfabet A-Z nama tipe_dokumen
        $types = Document::query()
            ->selectRaw('tipe_dokumen, count(*) as total')
            ->groupBy('tipe_dokumen')
            ->orderBy('tipe_dokumen', 'asc')
            ->get();

        $years = Document::query()->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('documents.index', [
            'documents' => $documents,
            'types' => $types,
            'years' => $years,
            'stats' => [
                'total' => Document::count(),
                'berlaku' => Document::where('status', 'Berlaku')->count(),
                'jenis' => Document::distinct('tipe_dokumen')->count('tipe_dokumen'),
                'tahunTerbaru' => Document::max('tahun'),
            ],
            'filters' => $request->only(['q', 'jenis', 'tahun', 'status']),
        ]);
    }

    public function show(Document $document)
    {
        $document->load(['mencabut', 'dicabutDengan', 'dicabutOleh', 'mencabutDokumenLain']);

        return view('documents.show', ['document' => $document]);
    }
}