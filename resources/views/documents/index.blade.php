@extends('layouts.app')

@section('title', 'Portal JDIH Universitas Insan Mahardika')

@section('content')
    @php
        // Mengurutkan jenis dokumen secara alfabetis (A-Z) untuk seluruh bagian
        $sortedTypes = collect($types)->sortBy('tipe_dokumen');
    @endphp

    <section class="relative overflow-hidden bg-blue-950 text-white">
        {{-- Real campus photo: portrait crop on mobile, landscape on md+ --}}
        <div class="absolute inset-0 bg-[url('/hero-banner-vertical.jpg')] bg-cover bg-center md:bg-[url('/hero-banner-horizontal.jpg')]" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-blue-950/90 via-blue-950/80 to-blue-950" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-16 md:py-20">
            <div class="mx-auto max-w-2xl animate-fade-in-up text-center">
                <p class="mb-3 text-sm font-medium uppercase tracking-wide text-blue-300">Portal resmi Universitas Insan Mahardika</p>
                <h1 class="text-4xl font-extrabold leading-tight md:text-5xl">Jaringan Dokumentasi dan Informasi Hukum</h1>
                <p class="mx-auto mt-4 max-w-lg text-blue-200">
                    Akses terpusat terhadap seluruh peraturan dan dokumen hukum Universitas Insan Mahardika —
                    terbuka, tertata, dan mudah ditelusuri.
                </p>
            </div>

            {{-- Advanced search card --}}
            <form action="{{ route('documents.index') }}#dokumen" method="get"
                class="animate-fade-in-up mx-auto mt-8 grid max-w-3xl gap-3 rounded-2xl bg-white p-4 shadow-2xl md:grid-cols-[2fr_1fr_1fr_auto] md:items-end"
                style="animation-delay: 80ms">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Kata kunci</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Judul, nomor, atau subjek…"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Jenis dokumen</label>
                    <select name="jenis" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800">
                        <option value="">Semua jenis</option>
                        @foreach ($sortedTypes as $type)
                            <option value="{{ $type->tipe_dokumen }}" @selected(($filters['jenis'] ?? '') === $type->tipe_dokumen)>{{ $type->tipe_dokumen }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Tahun</label>
                    <select name="tahun" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800">
                        <option value="">Semua tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected(($filters['tahun'] ?? '') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-blue-700 px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                    Cari Dokumen
                </button>
            </form>

            <div class="animate-fade-in-up mt-4 flex flex-wrap justify-center gap-2 text-xs text-blue-200" style="animation-delay: 140ms">
                <span>Populer:</span>
                <a href="{{ route('documents.index') }}?jenis=Keputusan#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900">Keputusan</a>
                <a href="{{ route('documents.index') }}?jenis=Dokumen+SPMI#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900">Dokumen SPMI</a>
                <a href="{{ route('documents.index') }}?tahun={{ now()->year }}#dokumen" class="rounded-full border border-blue-800 px-3 py-1 hover:bg-blue-900">{{ now()->year }}</a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10">
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
                    // Deskripsi spesifik, unik, dan informatif untuk setiap jenis dokumen
                    $tooltipDesc = match(strtolower($type->tipe_dokumen)) {
                        'peraturan rektor' => 'Regulasi tingkat universitas yang ditetapkan langsung oleh Rektor untuk mengatur penyelenggaraan tridharma, tata kelola, dan ketertiban kampus.',
                        'peraturan yayasan' => 'Ketentuan dasar, kebijakan strategis, dan regulasi internal yang ditetapkan oleh Yayasan penyelenggara universitas.',
                        'peraturan lainnya' => 'Kumpulan peraturan penunjang, aturan tambahan, atau regulasi pelengkap operasional institusi.',
                        'surat keputusan (sk) rektor', 'keputusan rektor' => 'Ketetapan administratif bersifat konkret dan individual dari Rektor, seperti pengangkatan pejabat, tim kerja, atau kebijakan operasional.',
                        'surat keputusan (sk) dekan', 'keputusan dekan' => 'Keputusan resmi tingkat fakultas yang ditetapkan oleh Dekan guna mengatur kelancaran akademik, kurikulum, dan kemahasiswaan.',
                        'surat keputusan (sk) yayasan' => 'Kebijakan ketetapan resmi dari pihak Yayasan terkait penugasan struktural maupun keputusan strategis kelembagaan.',
                        'keputusan lainnya' => 'Kumpulan Surat Keputusan (SK) penunjang tata kelola lainnya di lingkungan Universitas Insan Mahardika.',
                        'dokumen spmi' => 'Perangkat sistem, manual mutu, dan prosedur operasional baku untuk penjaminan mutu pendidikan tinggi secara berkala.',
                        'standar mutu spmi' => 'Kriteria, tolokukur, dan patokan baku tingkat pencapaian mutu penyelenggaraan tridharma perguruan tinggi.',
                        'naskah kerja sama', 'naskah kerjasama' => 'Dokumen legalitas kemitraan dan kesepakatan formal (MoU, MoA, serta PKS) antara universitas dengan instansi mitra.',
                        'pedoman', 'pedoman dan panduan' => 'Buku petunjuk teknis, panduan akademik, dan tata cara pelaksanaan kegiatan operasional civitas akademika.',
                        default => 'Kumpulan arsip dan dokumen resmi ' . $type->tipe_dokumen . ' yang diterbitkan oleh Universitas Insan Mahardika.'
                    };
                @endphp

                <!-- Pembungkus Card dengan efek Hover Tooltip -->
                <div class="relative group">
                    <a href="{{ route('documents.index') }}?jenis={{ urlencode($type->tipe_dokumen) }}#dokumen"
                        class="block rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-1 hover:border-blue-400 hover:shadow-md">
                        <div class="mb-3 flex items-center justify-between">
                            <!-- Container Ikon Profesional & Bervariasi -->
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-800 group-hover:bg-blue-900 group-hover:text-white transition-colors duration-200">
                                @php
                                    $t = strtolower($type->tipe_dokumen);
                                @endphp

                                @if(str_contains($t, 'peraturan'))
                                    {{-- Ikon Timbangan Keadilan (Clean Balance Scale) --}}
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m-5-2h10M4 7l8-3 8 3M5 7l3 6H2l3-6zm14 0l3 6h-6l3-6z"/>
                                    </svg>
                                @elseif(str_contains($t, 'keputusan') || str_contains($t, 'sk'))
                                    {{-- Ikon Dokumen Resmi / SK --}}
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif(str_contains($t, 'spmi') || str_contains($t, 'mutu'))
                                    {{-- Ikon Perisai Mutu (Shield Check) --}}
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                @elseif(str_contains($t, 'kerjasama') || str_contains($t, 'kerja sama'))
                                    {{-- Ikon Tautan / Kerja Sama Dokumen --}}
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                @elseif(str_contains($t, 'pedoman') || str_contains($t, 'panduan'))
                                    {{-- Ikon Buku / Panduan --}}
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                @else
                                    {{-- Ikon Folder Arsip Umum --}}
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Badge Total Dokumen -->
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $badgeColors[$index % count($badgeColors)] }}">
                                {{ $type->total }}
                            </span>
                        </div>

                        <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-700">{{ $type->tipe_dokumen }}</p>
                        <p class="text-xs text-slate-400">dokumen</p>
                    </a>

                    <!-- TOOLTIP SPESIFIK & ELEGAN -->
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

    <section id="dokumen" class="mx-auto max-w-6xl px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-900">Katalog peraturan</h2>
            @if (($filters['q'] ?? '') || ($filters['jenis'] ?? '') || ($filters['tahun'] ?? '') || ($filters['status'] ?? ''))
                <a href="{{ route('documents.index') }}#dokumen" class="inline-flex items-center gap-1 text-sm font-medium text-blue-700 hover:text-blue-900 hover:underline">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 4v6h6M20 20v-6h-6M4.5 15A8 8 0 0 0 19 9M19.5 9A8 8 0 0 0 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Atur ulang filter
                </a>
            @endif
        </div>

        <form action="{{ route('documents.index') }}#dokumen" method="get" class="mb-6 grid gap-4 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-3">
            <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Jenis dokumen</label>
                <select name="jenis" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" onchange="this.form.submit()">
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
                <select name="tahun" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">Semua tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(($filters['tahun'] ?? '') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="Berlaku" @selected(($filters['status'] ?? '') === 'Berlaku')>Berlaku</option>
                    <option value="Tidak Berlaku" @selected(($filters['status'] ?? '') === 'Tidak Berlaku')>Tidak Berlaku</option>
                </select>
            </div>
        </form>

        <p class="mb-4 text-sm text-slate-500">
            Menampilkan {{ $documents->count() }} dari {{ $documents->total() }} dokumen
        </p>

        <div class="grid gap-4">
            @forelse ($documents as $document)
                <a href="{{ route('documents.show', $document) }}" class="group flex items-start justify-between gap-4 rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">
                    <div>
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ $document->tipe_dokumen }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $document->status === 'Berlaku' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
                                {{ $document->status }}
                            </span>
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
    </section>
@endsection