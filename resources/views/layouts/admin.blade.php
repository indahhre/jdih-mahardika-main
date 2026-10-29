<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin JDIH UIM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 md:flex">
    {{-- Mobile top bar --}}
    <div class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-blue-950 px-4 py-3 text-blue-100 md:hidden">
        <div class="flex items-center gap-2">
            <img src="{{ asset('logo-uim.png') }}" alt="" class="h-8 w-8">
            <span class="text-sm font-bold text-white">Admin JDIH</span>
        </div>
        <details class="relative">
            <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-lg border border-blue-800">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </summary>
            <nav class="absolute right-0 top-full z-20 mt-2 flex w-56 flex-col gap-1 rounded-xl border border-blue-900 bg-blue-950 p-2 text-sm shadow-xl">
                @include('admin.partials.nav')
                <a href="{{ route('documents.index') }}" target="_blank" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer">Lihat portal publik ↗</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-blue-300 hover:bg-blue-900 hover:text-white cursor-pointer">Keluar</button>
                </form>
            </nav>
        </details>
    </div>

    {{-- Desktop sidebar --}}
    <aside class="hidden w-60 shrink-0 flex-col bg-blue-950 text-blue-100 md:flex">
        <div class="flex items-center justify-between gap-2 px-5 py-5">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo-uim.png') }}" alt="" class="h-9 w-9">
                <span class="text-sm font-bold text-white">Admin JDIH</span>
            </div>
            <a href="{{ route('documents.index') }}" target="_blank" title="Lihat portal publik"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-blue-300 hover:bg-blue-900 hover:text-white cursor-pointer">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M14 4h6v6M20 4l-9 9M6 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
        <nav class="mt-2 flex flex-1 flex-col gap-1 px-3 text-sm">
            @include('admin.partials.nav')
        </nav>
        <div class="px-5 py-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-300 hover:text-white cursor-pointer">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-4 md:px-6">
            <h1 class="text-lg font-semibold text-slate-900">@yield('title', 'Admin')</h1>

            @php
                $currentUser = auth()->user();
                $avatarLabel = $currentUser->name ?: $currentUser->email;
                $initials = collect(explode(' ', trim($avatarLabel)))
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->take(2)
                    ->implode('');
                $initials = mb_strtoupper($initials ?: '?');
            @endphp

            <details class="relative">
                <summary class="flex cursor-pointer list-none items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-800 text-sm font-semibold text-white cursor-pointer" title="{{ $avatarLabel }} · {{ $currentUser->role }}">
                        {{ $initials }}
                    </span>
                </summary>
                <div class="absolute right-0 top-full z-20 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-3 text-sm shadow-lg">
                    <p class="font-semibold text-slate-900">{{ $avatarLabel }}</p>
                    <p class="mb-3 text-xs text-slate-500">{{ $currentUser->role === 'super_admin' ? 'Super Admin' : 'Editor' }}</p>
                    <a href="{{ route('admin.account.edit') }}" class="mb-1 block rounded-lg px-2 py-1.5 hover:bg-slate-50 cursor-pointer">Pengaturan Akun</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-2 py-1.5 text-left text-red-600 hover:bg-red-50 cursor-pointer">Keluar</button>
                    </form>
                </div>
            </details>
        </header>

        <main class="p-4 md:p-6">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>