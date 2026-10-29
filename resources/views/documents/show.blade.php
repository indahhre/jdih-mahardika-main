@extends('layouts.app')

@section('title', $document->judul.' - JDIH Universitas Insan Mahardika')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10">
        <a href="{{ route('documents.index') }}" class="mb-6 inline-block text-sm text-blue-700 hover:underline">← Kembali ke daftar</a>

        <div class="mb-4 flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ $document->tipe_dokumen }}</span>
        </div>

        <h1 class="mb-4 text-2xl font-bold text-slate-900">{{ $document->judul }}</h1>

        <div class="mb-6 rounded-xl border {{ $document->status === 'Berlaku' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }} p-5">
            <div class="flex flex-wrap items-center gap-2 text-sm font-semibold {{ $document->status === 'Berlaku' ? 'text-green-900' : 'text-red-900' }}">
                <span>Status:</span>
                <span class="rounded-full px-3 py-1 text-xs {{ $document->status === 'Berlaku' ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                    {{ strtoupper($document->status) }}
                </span>
            </div>

            @if ($document->mencabut || $document->mencabut_teks || $document->dicabutDengan || $document->dicabut_dengan_teks || $document->dicabutOleh->isNotEmpty() || $document->mencabutDokumenLain->isNotEmpty())
                <div class="mt-3 space-y-1.5 border-t {{ $document->status === 'Berlaku' ? 'border-green-200' : 'border-red-200' }} pt-3 text-sm {{ $document->status === 'Berlaku' ? 'text-green-900' : 'text-red-900' }}">
                    @if ($document->mencabut)
                        <p>Dokumen ini <strong>mencabut</strong>: <a href="{{ route('documents.show', $document->mencabut) }}" class="text-blue-700 underline">{{ $document->mencabut->nomor }} — {{ $document->mencabut->judul }}</a></p>
                    @elseif ($document->mencabut_teks)
                        <p>Dokumen ini <strong>mencabut</strong>: {{ $document->mencabut_teks }} <em class="text-xs opacity-75">(dokumen sumber tidak ada di sistem ini)</em></p>
                    @endif

                    @if ($document->dicabutDengan)
                        <p>Dokumen ini <strong>dicabut dengan</strong>: <a href="{{ route('documents.show', $document->dicabutDengan) }}" class="text-blue-700 underline">{{ $document->dicabutDengan->nomor }} — {{ $document->dicabutDengan->judul }}</a></p>
                    @elseif ($document->dicabut_dengan_teks)
                        <p>Dokumen ini <strong>dicabut dengan</strong>: {{ $document->dicabut_dengan_teks }} <em class="text-xs opacity-75">(dokumen sumber tidak ada di sistem ini)</em></p>
                    @endif

                    @foreach ($document->dicabutOleh as $revoker)
                        <p>Dicabut oleh: <a href="{{ route('documents.show', $revoker) }}" class="text-blue-700 underline">{{ $revoker->nomor }} — {{ $revoker->judul }}</a></p>
                    @endforeach

                    @foreach ($document->mencabutDokumenLain as $revoked)
                        <p>Mencabut: <a href="{{ route('documents.show', $revoked) }}" class="text-blue-700 underline">{{ $revoked->nomor }} — {{ $revoked->judul }}</a></p>
                    @endforeach
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
                @if ($document->gdrive_preview_url)
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                        <iframe src="{{ $document->gdrive_preview_url }}" class="aspect-[3/4] w-full" loading="lazy" allow="autoplay" title="Pratinjau {{ $document->judul }}"></iframe>
                    </div>
                @endif

                <div class="{{ $document->gdrive_preview_url ? 'mt-4' : '' }} rounded-xl border border-slate-200 bg-white p-5">
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
