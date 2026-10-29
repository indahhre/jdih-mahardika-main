<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal JDIH Universitas Insan Mahardika')</title>
    <meta name="description" content="Jaringan Dokumentasi dan Informasi Hukum Universitas Insan Mahardika.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- FontAwesome untuk ikon jenis dokumen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="{{ route('documents.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo-uim.png') }}" alt="Logo Universitas Insan Mahardika" class="h-11 w-11">
                <span>
                    <strong class="block text-lg font-bold text-blue-900">JDIH Universitas Insan Mahardika</strong>
                    <small class="text-xs text-slate-500">Jaringan Dokumentasi dan Informasi Hukum</small>
                </span>
            </a>

            {{-- desktop nav --}}
            <nav class="hidden items-center gap-1 text-sm font-medium text-slate-600 md:flex">
                <a href="{{ route('documents.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-blue-800">Beranda</a>
                <a href="{{ route('documents.index') }}#dokumen" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-blue-800">Dokumen</a>

                <details class="group relative">
                    <summary class="flex cursor-pointer list-none items-center gap-1 rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-blue-800">
                        Jenis Dokumen
                        <svg class="h-3 w-3 transition group-open:rotate-180" viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5"/></svg>
                    </summary>
                    <div class="absolute left-0 top-full mt-1 w-64 max-h-80 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                        @foreach ($navTypes as $type)
                            <a href="{{ route('documents.index') }}?jenis={{ urlencode($type) }}#dokumen" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100">{{ $type }}</a>
                        @endforeach
                    </div>
                </details>

                <a href="{{ route('faq') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-blue-800">Tentang</a>
                <a href="{{ route('documents.index') }}#kontak" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-blue-800">Kontak</a>
            </nav>

            {{-- mobile nav --}}
            <details class="md:hidden">
                <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-slate-200">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </summary>
                <nav class="absolute inset-x-0 top-full flex flex-col gap-1 border-b border-slate-200 bg-white p-4 text-sm font-medium text-slate-600 shadow-lg">
                    <a href="{{ route('documents.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">Beranda</a>
                    <a href="{{ route('documents.index') }}#dokumen" class="rounded-lg px-3 py-2 hover:bg-slate-100">Dokumen</a>
                    <p class="px-3 pt-2 text-xs font-semibold uppercase text-slate-400">Jenis Dokumen</p>
                    @foreach ($navTypes as $type)
                        <a href="{{ route('documents.index') }}?jenis={{ urlencode($type) }}#dokumen" class="rounded-lg px-3 py-2 hover:bg-slate-100">{{ $type }}</a>
                    @endforeach
                    <a href="{{ route('faq') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">Tentang</a>
                    <a href="{{ route('documents.index') }}#kontak" class="rounded-lg px-3 py-2 hover:bg-slate-100">Kontak</a>
                </nav>
            </details>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer id="kontak" class="mt-16 border-t border-slate-200 bg-blue-950 text-blue-100">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-3">
            <div class="flex items-start gap-3">
                <img src="{{ asset('logo-uim.png') }}" alt="" class="h-12 w-12">
                <div>
                    <strong class="block text-white">JDIH Universitas Insan Mahardika</strong>
                    <span class="text-sm text-blue-200">Universitas Insan Mahardika</span>
                </div>
            </div>
            <div class="text-sm">
                <p class="mb-1 font-semibold text-white">Hubungi kami</p>
                <a href="mailto:uimakademik@mahardika.ac.id" class="block text-blue-200 hover:text-white">uimakademik@mahardika.ac.id</a>
            </div>
            <div class="text-sm">
                <p class="mb-1 font-semibold text-white">Tautan terkait</p>
                <a href="https://mahardika.ac.id" class="block text-blue-200 hover:text-white">Situs utama ↗</a>
                <a href="https://lpm.mahardika.ac.id" class="block text-blue-200 hover:text-white">LP3M ↗</a>
            </div>
        </div>
        <div class="border-t border-blue-900/60 py-4 text-center text-xs text-blue-300">
            © {{ date('Y') }} Universitas Insan Mahardika — Jaringan Dokumentasi dan Informasi Hukum
        </div>
    </footer>
</body>
</html>