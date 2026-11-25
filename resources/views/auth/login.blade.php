<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Pengelola - JDIH Universitas Insan Mahardika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen w-full bg-[#fdf8f4] text-slate-800 flex flex-col justify-between overflow-x-hidden">

    {{-- Top Navigation / Brand Header --}}
    <header class="w-full px-6 py-3.5 sm:px-10 lg:px-14 flex items-center justify-between shrink-0">
        <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2.5 group cursor-pointer">
            <img src="{{ asset('logo-uim.png') }}" alt="Logo UIM" class="h-9 w-9 object-contain transition-transform duration-200 group-hover:scale-105">
            <div>
                <p class="text-sm font-extrabold tracking-tight text-slate-900 group-hover:text-blue-800 transition-colors">JDIH Universitas Insan Mahardika</p>
                <p class="text-[11px] text-slate-500">Jaringan Dokumentasi dan Informasi Hukum</p>
            </div>
        </a>

        <a
            href="{{ route('documents.index') }}"
            class="inline-flex items-center gap-1.5 rounded-full border border-slate-200/90 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-600 shadow-2xs transition hover:border-blue-300 hover:text-blue-700 cursor-pointer"
        >
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Portal Publik
        </a>
    </header>

    {{-- Main Full-Screen Content --}}
    <main class="mx-auto flex w-full max-w-6xl flex-1 items-center px-6 py-2 sm:px-10 lg:px-14">
        <div class="grid w-full items-center gap-8 lg:grid-cols-12 lg:gap-10">

            {{-- Left Side: Illustration appearing from the side on the same unified canvas --}}
            <div class="animate-slide-in-side order-2 flex flex-col items-center justify-center lg:order-1 lg:col-span-7">
                <div class="relative flex w-full items-center justify-center">
                    <img
                        src="{{ asset('login-illustration.png') }}?v={{ filemtime(public_path('login-illustration.png')) }}"
                        alt="Ilustrasi Pengelola JDIH Universitas Insan Mahardika"
                        class="animate-float-gentle h-auto max-h-[280px] sm:max-h-[320px] lg:max-h-[340px] xl:max-h-[380px] w-auto object-contain select-none"
                    >
                </div>
                <div class="mt-3 max-w-md text-center">
                    <h2 class="text-base font-bold text-slate-800 sm:text-lg">
                        Sistem Pengelolaan Dokumentasi Hukum Terpadu
                    </h2>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500 sm:text-sm">
                        Kelola peraturan, surat keputusan, dan dokumen mutu Universitas Insan Mahardika dalam satu ekosistem digital yang terstruktur.
                    </p>
                </div>
            </div>

            {{-- Right Side: Clean Login Form --}}
            <div class="animate-fade-in-up order-1 mx-auto w-full max-w-md lg:order-2 lg:col-span-5">
                <div class="rounded-3xl border border-slate-200/80 bg-white p-7 shadow-xl shadow-slate-900/5 sm:p-8">
                    <div class="mb-5">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                            Panel Pengelola
                        </span>
                        <h1 class="mt-2.5 text-2xl font-extrabold tracking-tight text-slate-900">
                            Selamat Datang
                        </h1>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">
                            Silakan masuk dengan akun pengelola JDIH Anda
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs sm:text-sm text-red-700">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                                Email
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="nama@mahardika.ac.id"
                                    required
                                    autofocus
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2.5 pl-10 pr-4 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Masukkan kata sandi"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2.5 pl-10 pr-10 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                >
                                <button
                                    type="button"
                                    onclick="const input = document.getElementById('password'); const isPwd = input.type === 'password'; input.type = isPwd ? 'text' : 'password'; this.querySelector('[data-eye-open]').classList.toggle('hidden', !isPwd); this.querySelector('[data-eye-closed]').classList.toggle('hidden', isPwd);"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 cursor-pointer"
                                    title="Tampilkan / Sembunyikan Kata Sandi"
                                >
                                    <svg data-eye-closed class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg data-eye-open class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="group flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-700/20 transition-all duration-200 hover:bg-blue-800 hover:shadow-lg hover:shadow-blue-800/30 focus:outline-none focus:ring-4 focus:ring-blue-600/20 cursor-pointer"
                        >
                            <span>Masuk Sekarang</span>
                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    {{-- Subtle Full-Screen Footer --}}
    <footer class="w-full px-6 py-2.5 text-center text-xs text-slate-400 sm:px-10 lg:px-14 shrink-0">
        &copy; {{ date('Y') }} JDIH Universitas Insan Mahardika. Hak cipta dilindungi.
    </footer>

</body>
</html>
