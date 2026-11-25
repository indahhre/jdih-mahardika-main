@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Pengguna Sistem</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akses, peran, dan status akun pengelola portal JDIH</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800 transition cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Akun
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4.5 font-semibold text-slate-900 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4.5 text-slate-500 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ $user->role === 'super_admin' ? 'Super Admin' : 'Editor' }}
                            </span>
                        </td>
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-500/20' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4.5 text-center whitespace-nowrap">
                            <div class="inline-flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 hover:text-blue-800 transition cursor-pointer shadow-sm ring-1 ring-inset ring-blue-700/10">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Ubah
                                </a>

                                @if ($user->id === auth()->id())
                                    <span class="inline-flex items-center rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-medium text-slate-400 italic border border-slate-200/60" title="Anda tidak dapat menghapus akun yang sedang digunakan">
                                        Akun Anda
                                    </span>
                                @else
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 hover:text-red-800 transition cursor-pointer shadow-sm ring-1 ring-inset ring-red-700/10">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
