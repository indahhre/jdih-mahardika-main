<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Pengelola - JDIH UIM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4">
    <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-6 text-center">
            <img src="{{ asset('logo-uim.png') }}" alt="" class="mx-auto mb-3 h-12 w-12">
            <h1 class="text-lg font-bold text-slate-900">Masuk Pengelola JDIH</h1>
            <p class="text-sm text-slate-500">Universitas Insan Mahardika</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi</label>
                <input type="password" name="password" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full rounded-lg bg-blue-700 py-2 text-sm font-medium text-white hover:bg-blue-800">
                Masuk
            </button>
        </form>

        <a href="{{ route('documents.index') }}" class="mt-4 block text-center text-xs text-slate-400 hover:text-slate-600">← Kembali ke portal publik</a>
    </div>
</body>
</html>
