@extends('layouts.app')

@section('title', $document->judul.' - JDIH Universitas Insan Mahardika')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10">
        <a href="{{ route('documents.index') }}" class="mb-6 inline-block text-sm text-blue-700 hover:underline">← Kembali ke daftar</a>

        <div class="mb-4 flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ $document->tipe_dokumen }}</span>
        </div>

        <h1 class="mb-4 text-2xl font-bold text-slate-900">{{ $document->judul }}</h1>

        @php
            // Kumpulkan daftar dokumen yang dicabut oleh dokumen ini
            $mencabutList = collect();
            if ($document->mencabut) {
                $mencabutList->push($document->mencabut);
            }
            foreach ($document->mencabutDokumenRelasi as $d) {
                $mencabutList->push($d);
            }
            foreach ($document->dicabutOleh as $d) {
                $mencabutList->push($d);
            }
            $mencabutList = $mencabutList->unique('id');

            // Kumpulkan daftar dokumen yang mencabut dokumen ini
            $dicabutOlehList = collect();
            if ($document->dicabutDengan) {
                $dicabutOlehList->push($document->dicabutDengan);
            }
            foreach ($document->dicabutOlehRelasi as $d) {
                $dicabutOlehList->push($d);
            }
            foreach ($document->mencabutDokumenLain as $d) {
                $dicabutOlehList->push($d);
            }
            $dicabutOlehList = $dicabutOlehList->unique('id');
        @endphp

        <div class="mb-6 rounded-2xl border {{ $document->status === 'Berlaku' ? 'border-emerald-200 bg-emerald-50/70' : 'border-rose-200 bg-rose-50/70' }} p-5 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $document->status === 'Berlaku' ? 'text-emerald-900' : 'text-rose-900' }}">Status Hukum:</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $document->status === 'Berlaku' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-rose-600 text-white shadow-xs' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                        {{ strtoupper($document->status) }}
                    </span>
                </div>
            </div>

            {{-- 1. JIKA DOKUMEN INI DICABUT / TIDAK BERLAKU --}}
            @if ($document->status === 'Tidak Berlaku' || $dicabutOlehList->isNotEmpty() || $document->dicabut_dengan_teks)
                <div class="mt-4 border-t border-rose-200/80 pt-4 text-sm text-rose-950">
                    <p class="font-bold flex items-center gap-1.5 text-rose-900 mb-2">
                        <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Dokumen ini dinyatakan TIDAK BERLAKU karena telah dicabut / digantikan oleh:
                    </p>
                    <div class="space-y-2 mt-2">
                        @foreach ($dicabutOlehList as $revoker)
                            <div class="flex items-start justify-between gap-3 rounded-xl border border-rose-200 bg-white p-3.5 shadow-2xs">
                                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                    <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800 shrink-0 mt-0.5">Berlaku</span>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('documents.show', $revoker) }}" class="font-semibold text-blue-700 hover:text-blue-900 hover:underline">
                                            {{ $revoker->judul }}
                                        </a>
                                        <p class="text-xs text-slate-500 mt-0.5">Nomor: {{ $revoker->nomor }} · Tahun {{ $revoker->tahun }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('documents.show', $revoker) }}" class="shrink-0 text-xs font-semibold text-blue-700 hover:underline">Lihat Dokumen →</a>
                            </div>
                        @endforeach

                        @if ($document->dicabut_dengan_teks)
                            <div class="rounded-xl border border-rose-200 bg-white p-3 text-xs text-slate-700">
                                <span class="font-semibold text-rose-800">Keterangan Pencabutan:</span> {{ $document->dicabut_dengan_teks }}
                            </div>
                        @endif

                        @if ($dicabutOlehList->isEmpty() && !$document->dicabut_dengan_teks)
                            <p class="text-xs text-rose-700 italic mt-1">Dokumen ini telah dinonaktifkan atau digantikan oleh regulasi baru.</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 2. JIKA DOKUMEN INI MENCABUT DOKUMEN LAIN --}}
            @if ($mencabutList->isNotEmpty() || $document->mencabut_teks)
                <div class="mt-4 border-t {{ $document->status === 'Berlaku' ? 'border-emerald-200/80 text-emerald-950' : 'border-rose-200/80 text-slate-800' }} pt-4 text-sm">
                    <p class="font-bold flex items-center gap-1.5 {{ $document->status === 'Berlaku' ? 'text-emerald-900' : 'text-slate-800' }} mb-2">
                        <svg class="h-4 w-4 {{ $document->status === 'Berlaku' ? 'text-emerald-600' : 'text-slate-500' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Dokumen ini MENCABUT / menggantikan dokumen berikut:
                    </p>
                    <div class="space-y-2 mt-2">
                        @foreach ($mencabutList as $revoked)
                            <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-200 bg-white p-3.5 shadow-2xs">
                                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                    <span class="rounded-md bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-800 shrink-0 mt-0.5">Tidak Berlaku</span>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('documents.show', $revoked) }}" class="font-semibold text-blue-700 hover:text-blue-900 hover:underline">
                                            {{ $revoked->judul }}
                                        </a>
                                        <p class="text-xs text-slate-500 mt-0.5">Nomor: {{ $revoked->nomor }} · Tahun {{ $revoked->tahun }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('documents.show', $revoked) }}" class="shrink-0 text-xs font-semibold text-blue-700 hover:underline">Lihat Dokumen →</a>
                            </div>
                        @endforeach

                        @if ($document->mencabut_teks)
                            <div class="rounded-xl border border-slate-200 bg-white p-3 text-xs text-slate-700">
                                <span class="font-semibold text-slate-800">Catatan Dokumen yang Dicabut:</span> {{ $document->mencabut_teks }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="grid gap-8 md:grid-cols-2">
            <div>
                <table class="w-full overflow-hidden rounded-xl border border-slate-200 text-sm">
                    <tbody>
                        @foreach ([
                            'Tipe Dokumen' => $document->tipe_dokumen,
                            'Nomor' => $document->nomor,
                            'Tahun' => $document->tahun,
                            'Tempat Penetapan' => $document->tempat_penetapan ?? '-',
                            'Tanggal Penetapan' => $document->tanggal_penetapan?->translatedFormat('d F Y') ?? '-',
                            'Tanggal Pengundangan' => $document->tanggal_pengundangan?->translatedFormat('d F Y') ?? '-',
                            'Penandatangan' => $document->penandatangan ?? '-',
                            'Sumber' => $document->sumber ?? '-',
                            'Subjek' => $document->subjek ?? '-',
                        ] as $label => $value)
                            <tr class="odd:bg-white even:bg-slate-50">
                                <td class="w-48 px-4 py-3 font-medium text-slate-500">{{ $label }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($document->ringkasan)
                    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-5">
                        <h2 class="mb-2 font-semibold text-slate-900">Ringkasan</h2>
                        <p class="text-sm text-slate-600">{{ $document->ringkasan }}</p>
                    </div>
                @endif
            </div>

            <div>
                {{-- PREVIEW PDF OTOMATIS (Mendukung Google Drive Preview atau File PDF Langsung) --}}
                @php
                    $pdfPreviewUrl = $document->gdrive_preview_url ?? $document->file_url;
                @endphp

                @if ($pdfPreviewUrl)
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-xs">
                        <iframe src="{{ $pdfPreviewUrl }}" class="aspect-[3/4] w-full" loading="lazy" allow="autoplay" title="Pratinjau {{ $document->judul }}"></iframe>
                    </div>
                @endif

                <div class="{{ $pdfPreviewUrl ? 'mt-4' : '' }} rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="mb-3 font-semibold text-slate-900">Aksi Dokumen</h2>
                    <div class="flex flex-col gap-2">
                        @if ($document->file_url)
                            <a href="{{ $document->file_url }}" target="_blank" class="rounded-lg bg-blue-700 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-800">Baca / Unduh PDF</a>
                        @endif
                        @if ($document->gdrive_url)
                            <a href="{{ $document->gdrive_url }}" target="_blank" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Buka di Google Drive ↗</a>
                        @endif
                        @if (! $document->file_url && ! $document->gdrive_url)
                            <p class="text-sm text-slate-400">Berkas belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection