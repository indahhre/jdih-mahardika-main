@extends('layouts.app')

@section('title', 'Portal JDIH Universitas Insan Mahardika')

@section('content')
    @php
        // Prioritaskan "Dokumen SPMI" di urutan pertama, selebihnya diurutkan A-Z secara otomatis
        $sortedTypes = collect($types)->sort(function ($a, $b) {
            $aName = strtolower(trim($a->tipe_dokumen));
            $bName = strtolower(trim($b->tipe_dokumen));

            if ($aName === 'dokumen spmi') return -1;
            if ($bName === 'dokumen spmi') return 1;

            return strcmp($a->tipe_dokumen, $b->tipe_dokumen);
        });
    @endphp

    <section class="relative z-30 bg-blue-950 text-white overflow-visible">
        {{-- hero background: portrait mobile, landscape md+ --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
            <div class="absolute inset-0 bg-[url('/hero-banner-vertical.jpg')] bg-cover bg-center md:bg-[url('/hero-banner-horizontal.jpg')]"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-blue-950/90 via-blue-950/80 to-blue-950"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-4 py-16 md:py-20">
            <div class="mx-auto max-w-2xl animate-fade-in-up text-center">
                <p class="mb-3 text-sm font-medium uppercase tracking-wide text-blue-300">Portal resmi Universitas Insan Mahardika</p>
                <h1 class="text-4xl font-extrabold leading-tight md:text-5xl">Jaringan Dokumentasi dan Informasi Hukum</h1>
                <p class="mx-auto mt-4 max-w-lg text-blue-200">
                    Akses terpusat terhadap seluruh peraturan dan dokumen hukum Universitas Insan Mahardika —
                    terbuka, tertata, dan mudah ditelusuri.
                </p>
            </div>

            {{-- search card with live search suggestions --}}
            <form action="{{ route('documents.index') }}#dokumen" method="get"
                class="animate-fade-in-up relative z-30 mx-auto mt-8 grid max-w-3xl gap-3 rounded-2xl bg-white p-4 shadow-2xl md:grid-cols-[2fr_1fr_1fr_auto] md:items-end"
                style="animation-delay: 80ms">
                <div class="relative" data-public-livesearch>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Kata kunci</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Judul, nomor, atau subjek…"
                            autocomplete="off"
                            data-public-search-input
                            class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-14 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 gap-1.5">
                            <div data-public-search-spinner class="hidden animate-spin text-blue-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </div>
                            <button type="button" data-public-search-clear class="{{ ($filters['q'] ?? '') ? '' : 'hidden' }} rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition cursor-pointer" title="Hapus pencarian">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Floating live suggestions dropdown -->
                    <div data-public-search-dropdown class="hidden absolute left-0 top-full z-50 mt-2 w-full md:w-[600px] lg:w-[650px] max-w-[calc(100vw-2.5rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl ring-1 ring-black/10 text-slate-800">
                        <div data-public-search-results></div>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Jenis dokumen</label>
                    <select name="jenis" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 cursor-pointer">
                        <option value="">Semua jenis</option>
                        @foreach ($sortedTypes as $type)
                            <option value="{{ $type->tipe_dokumen }}" @selected(($filters['jenis'] ?? '') === $type->tipe_dokumen)>{{ $type->tipe_dokumen }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Tahun</label>
                    <select name="tahun" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 cursor-pointer">
                        <option value="">Semua tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected(($filters['tahun'] ?? '') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-blue-700 px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800 transition cursor-pointer">
                    Cari Dokumen
                </button>
            </form>

            <div class="animate-fade-in-up relative z-10 mt-4 flex flex-wrap justify-center gap-2 text-xs text-blue-200" style="animation-delay: 140ms">
                <span>Populer:</span>
                <a href="{{ route('documents.index') }}?jenis=Surat+Keputusan#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900 transition cursor-pointer">Surat Keputusan</a>
                <a href="{{ route('documents.index') }}?jenis=Peraturan#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900 transition cursor-pointer">Peraturan</a>
                <a href="{{ route('documents.index') }}?jenis=Dokumen+SPMI#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900 transition cursor-pointer">Dokumen SPMI</a>
                <a href="{{ route('documents.index') }}?tahun={{ now()->year }}#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900 transition cursor-pointer">{{ now()->year }}</a>
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto max-w-6xl px-4 py-10">
        <div class="mb-5 flex items-end justify-between">
            <h2 class="text-xl font-bold text-slate-900">Jelajahi berdasarkan jenis</h2>
            <a href="{{ route('documents.index') }}#dokumen" class="text-sm font-medium text-blue-700 hover:underline">Lihat semua →</a>
        </div>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @php
                $badgeColors = ['bg-blue-50 text-blue-700', 'bg-amber-50 text-amber-700', 'bg-emerald-50 text-emerald-700', 'bg-rose-50 text-rose-700', 'bg-violet-50 text-violet-700'];
                $sortedCardTypes = $sortedTypes->values();
            @endphp
            @foreach ($sortedCardTypes as $index => $type)
                @php
                    $tooltipDesc = match(strtolower($type->tipe_dokumen)) {
                        'peraturan', 'peraturan rektor', 'peraturan yayasan', 'peraturan lainnya', 'peraturan institusi / rektor' => 'Regulasi resmi, ketentuan dasar, dan kebijakan strategis yang berlaku mengikat di lingkungan Universitas Insan Mahardika.',
                        'surat keputusan', 'surat keputusan (sk) rektor', 'surat keputusan (sk) dekan', 'surat keputusan (sk) yayasan', 'keputusan', 'keputusan lainnya' => 'Ketetapan administratif resmi bersifat konkret dari pimpinan universitas, fakultas, maupun yayasan untuk mendukung tata kelola institusi.',
                        'dokumen spmi', 'standar mutu spmi' => 'Perangkat sistem penjaminan mutu internal yang mencakup kebijakan mutu, standar mutu, manual, formulir, dan instrumen monev perguruan tinggi.',
                        'naskah kerja sama', 'naskah kerjasama' => 'Dokumen legalitas kemitraan dan kesepakatan formal (MoU, MoA, serta PKS) antara universitas dengan instansi mitra.',
                        'pedoman', 'pedoman dan panduan', 'pedoman / panduan' => 'Buku petunjuk teknis, panduan operasional, dan tata cara pelaksanaan kegiatan tridharma perguruan tinggi.',
                        'standar operasional prosedur (sop)' => 'Panduan alur kerja terstandarisasi untuk menjamin kepastian dan konsistensi operasional setiap unit kerja.',
                        'statuta' => 'Pedoman dasar pengelolaan institusi pendidikan tinggi yang memuat visi, misi, asas, dan tata kelola universitas.',
                        'laporan evaluasi / ami / monev' => 'Laporan hasil evaluasi, audit mutu internal, dan monitoring berkala program kerja institusi.',
                        default => 'Kumpulan arsip dan dokumen resmi ' . $type->tipe_dokumen . ' yang diterbitkan oleh Universitas Insan Mahardika.'
                    };
                @endphp

                <div class="relative group">
                    <a href="{{ route('documents.index') }}?jenis={{ urlencode($type->tipe_dokumen) }}#dokumen"
                        class="block rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-1 hover:border-blue-400 hover:shadow-md cursor-pointer">
                        <div class="mb-3 flex items-center justify-between">

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-800 group-hover:bg-blue-900 group-hover:text-white transition-colors duration-200">
                                @php
                                    $t = strtolower($type->tipe_dokumen);
                                @endphp

                                @if(str_contains($t, 'peraturan'))
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m-5-2h10M4 7l8-3 8 3M5 7l3 6H2l3-6zm14 0l3 6h-6l3-6z"/>
                                    </svg>
                                @elseif(str_contains($t, 'keputusan') || str_contains($t, 'sk'))
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif(str_contains($t, 'spmi') || str_contains($t, 'mutu'))
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                @elseif(str_contains($t, 'kerjasama') || str_contains($t, 'kerja sama'))
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                @elseif(str_contains($t, 'pedoman') || str_contains($t, 'panduan'))
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>

                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $badgeColors[$index % count($badgeColors)] }}">
                                {{ $type->total }}
                            </span>
                        </div>

                        <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-700">{{ $type->tipe_dokumen }}</p>
                        <p class="text-xs text-slate-400">dokumen</p>
                    </a>

                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 hidden group-hover:block w-72 p-4 bg-white text-slate-900 text-xs rounded-xl shadow-xl shadow-slate-900/10 border border-slate-200 z-50 pointer-events-none transition-all">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-slate-100">
                            <span class="p-1 rounded-md bg-blue-50 text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <span class="font-bold text-blue-900 text-[13px]">Ruang Lingkup</span>
                        </div>
                        <p class="leading-relaxed text-slate-700 font-normal">{{ $tooltipDesc }}</p>
                        <div class="absolute top-full left-1/2 -ml-2 border-solid border-t-white border-t-8 border-x-transparent border-x-8 border-b-0 drop-shadow-[0_2px_1px_rgba(0,0,0,0.08)]"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-center">
                <strong class="block text-2xl font-bold text-slate-900">{{ $stats['total'] }}</strong>
                <span class="text-sm text-slate-500">Total dokumen</span>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-center">
                <strong class="block text-2xl font-bold text-slate-900">{{ $stats['berlaku'] }}</strong>
                <span class="text-sm text-slate-500">Dokumen berlaku</span>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-center">
                <strong class="block text-2xl font-bold text-slate-900">{{ $stats['jenis'] }}</strong>
                <span class="text-sm text-slate-500">Jenis dokumen</span>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-center">
                <strong class="block text-2xl font-bold text-slate-900">{{ $stats['tahunTerbaru'] }}</strong>
                <span class="text-sm text-slate-500">Pembaruan terbaru</span>
            </div>
        </div>
    </section>

    <section id="dokumen" class="relative z-10 mx-auto max-w-6xl px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-900">Katalog peraturan</h2>
            @if (($filters['q'] ?? '') || ($filters['jenis'] ?? '') || ($filters['tahun'] ?? '') || ($filters['status'] ?? ''))
                <a href="{{ route('documents.index') }}#dokumen" class="inline-flex items-center gap-1 text-sm font-medium text-blue-700 hover:text-blue-900 hover:underline">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 4v6h6M20 20v-6h-6M4.5 15A8 8 0 0 0 19 9M19.5 9A8 8 0 0 0 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Atur ulang filter
                </a>
            @endif
        </div>

        <form action="{{ route('documents.index') }}#dokumen" method="get" data-live-search-form data-target="#public-catalog-container" class="mb-6 grid gap-4 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-4">
            <div class="relative">
                <label class="mb-1 block text-xs font-medium text-slate-500">Kata kunci</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari judul / nomor…"
                        autocomplete="off"
                        data-live-search-input
                        class="w-full rounded-lg border border-slate-300 py-2 pl-8 pr-12 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                        <div data-live-search-spinner class="hidden animate-spin text-blue-600">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </div>
                        <button type="button" data-live-search-clear class="{{ ($filters['q'] ?? '') ? '' : 'hidden' }} rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition cursor-pointer" title="Hapus">
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Jenis dokumen</label>
                <select name="jenis" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua jenis</option>
                    @foreach ($sortedTypes as $type)
                        <option value="{{ $type->tipe_dokumen }}" @selected(($filters['jenis'] ?? '') === $type->tipe_dokumen)>
                            {{ $type->tipe_dokumen }} ({{ $type->total }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Tahun</label>
                <select name="tahun" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(($filters['tahun'] ?? '') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua status</option>
                    <option value="Berlaku" @selected(($filters['status'] ?? '') === 'Berlaku')>Berlaku</option>
                    <option value="Tidak Berlaku" @selected(($filters['status'] ?? '') === 'Tidak Berlaku')>Tidak Berlaku</option>
                </select>
            </div>
        </form>

        <div id="public-catalog-container">
            <p class="mb-4 text-sm text-slate-500">
                Menampilkan {{ $documents->count() }} dari {{ $documents->total() }} dokumen
            </p>

            <div class="grid gap-4">
                @forelse ($documents as $document)
                    <a href="{{ route('documents.show', $document) }}" class="group flex items-start justify-between gap-4 rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">
                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ $document->tipe_dokumen }}</span>
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $document->status === 'Berlaku' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $document->status === 'Berlaku' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $document->status }}
                                </span>

                                @if ($document->status === 'Tidak Berlaku')
                                    @php
                                        $revoker = $document->dicabutDengan ?? $document->dicabutOlehRelasi->first();
                                    @endphp
                                    @if ($revoker)
                                        <span class="text-xs text-rose-700 font-medium">
                                            · Dicabut oleh: <span class="font-semibold underline">{{ $revoker->nomor }}</span>
                                        </span>
                                    @elseif ($document->dicabut_dengan_teks)
                                        <span class="text-xs text-rose-700 font-medium">
                                            · Dicabut: {{ $document->dicabut_dengan_teks }}
                                        </span>
                                    @endif
                                @else
                                    @php
                                        $revoked = $document->mencabut ?? $document->mencabutDokumenRelasi->first();
                                    @endphp
                                    @if ($revoked)
                                        <span class="text-xs text-emerald-700 font-medium">
                                            · Mencabut: <span class="font-semibold">{{ $revoked->nomor }}</span>
                                        </span>
                                    @endif
                                @endif
                            </div>
                            <h3 class="font-semibold text-slate-900">{{ $document->judul }}</h3>
                            <p class="mt-1 text-sm text-slate-500">Nomor {{ $document->nomor }} · Tahun {{ $document->tahun }}</p>
                        </div>
                        <span class="shrink-0 text-blue-700 transition-transform duration-200 group-hover:translate-x-1">Lihat detail →</span>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                        Dokumen tidak ditemukan. Coba ubah kata kunci atau filter.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $documents->links() }}
            </div>
        </div>
    </section>
@endsection