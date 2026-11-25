<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query()
            ->whereNull('archived_at');

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

        // Live Search JSON endpoint untuk suggestion dropdown
        if ($request->wantsJson() || $request->query('format') === 'json') {
            $total = (clone $query)->count();
            $results = (clone $query)
                ->orderByDesc('tahun')
                ->orderByDesc('id')
                ->take(6)
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'judul' => $doc->judul,
                        'nomor' => $doc->nomor,
                        'tahun' => $doc->tahun,
                        'tipe_dokumen' => $doc->tipe_dokumen,
                        'status' => $doc->status,
                        'url' => route('documents.show', $doc),
                    ];
                });

            return response()->json([
                'total' => $total,
                'results' => $results,
            ]);
        }

        $documents = $query->with(['dicabutDengan', 'mencabut', 'mencabutDokumenRelasi', 'dicabutOlehRelasi'])->orderByDesc('tahun')->orderByDesc('id')->paginate(12)->withQueryString();

        // group by tipe, sort A-Z
        $types = Document::query()
            ->whereNull('archived_at')
            ->selectRaw('tipe_dokumen, count(*) as total')
            ->groupBy('tipe_dokumen')
            ->orderBy('tipe_dokumen', 'asc')
            ->get();

        $years = Document::query()->whereNull('archived_at')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('documents.index', [
            'documents' => $documents,
            'types' => $types,
            'years' => $years,
            'stats' => [
                'total' => Document::whereNull('archived_at')->count(),
                'berlaku' => Document::whereNull('archived_at')->where('status', 'Berlaku')->count(),
                'jenis' => Document::whereNull('archived_at')->distinct('tipe_dokumen')->count('tipe_dokumen'),
                'tahunTerbaru' => Document::whereNull('archived_at')->max('tahun'),
            ],
            'filters' => $request->only(['q', 'jenis', 'tahun', 'status']),
        ]);
    }

    public function show(Document $document)
    {
        $document->load(['mencabut', 'dicabutDengan', 'dicabutOleh', 'mencabutDokumenLain', 'mencabutDokumenRelasi', 'dicabutOlehRelasi']);

        return view('documents.show', ['document' => $document]);
    }
}