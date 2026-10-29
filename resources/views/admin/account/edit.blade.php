@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
    <div class="mb-6">
    </div>

    <form method="POST" action="{{ route('admin.account.update') }}" class="w-full space-y-6">
        @csrf
        @method('PUT')

        <!-- Card Informasi Profil -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-slate-900">Informasi Profil</h2>
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                <p class="mt-1 text-xs text-slate-400">Email login belum bisa diubah sendiri, hubungi super admin.</p>
            </div>
        </div>

        <!-- Card Ganti Kata Sandi -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-1 text-sm font-semibold text-slate-900">Ganti Kata Sandi</h2>
            <p class="mb-4 text-xs text-slate-500">Kosongkan jika tidak ingin mengganti kata sandi.</p>
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" placeholder="Masukkan sandi saat ini"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi Baru</label>
                <input type="password" name="password" placeholder="Masukkan sandi baru"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi sandi baru"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-medium text-white hover:bg-blue-800 transition cursor-pointer shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection