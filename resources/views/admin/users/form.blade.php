@extends('layouts.admin')

@section('title', $user->exists ? 'Ubah Akun' : 'Tambah Akun')

@section('content')
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-lg space-y-6">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="editor" @selected(old('role', $user->role) === 'editor')>Editor — tambah &amp; ubah dokumen</option>
                    <option value="super_admin" @selected(old('role', $user->role) === 'super_admin')>Super Admin — akses penuh + hapus dokumen + kelola pengguna</option>
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->exists ? $user->is_active : true))>
                Akun aktif
            </label>
            @unless ($user->exists)
                <p class="text-xs text-slate-500">Kata sandi sementara akan dibuatkan otomatis dan ditampilkan setelah akun dibuat.</p>
            @endunless
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-medium text-white hover:bg-blue-800">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm text-slate-600 hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection
