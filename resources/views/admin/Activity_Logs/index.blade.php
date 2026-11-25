@extends('layouts.admin')

@section('title', 'Log Aktivitas Admin')

@section('content')
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs text-slate-500 mt-0.5">Memantau seluruh rekam jejak tindakan dan operasional admin.</p>
        </div>

        <!-- search form -->
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" data-live-search-form data-target="#admin-logs-container" class="flex items-center gap-2">
            <div class="relative w-72">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas, admin, atau aksi..."
                    autocomplete="off"
                    data-live-search-input
                    class="w-full rounded-lg border border-slate-300 bg-white py-1.5 pl-8 pr-12 text-xs text-slate-800 transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                    <div data-live-search-spinner class="hidden animate-spin text-blue-600">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                    <button type="button" data-live-search-clear class="{{ request('q') ? '' : 'hidden' }} rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition cursor-pointer" title="Hapus pencarian">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-900 transition shrink-0 cursor-pointer">
                Cari
            </button>
        </form>
    </div>

    <div id="admin-logs-container">
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Admin / Pengguna</th>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Keterangan Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 text-xs">
                                {{ $log->user->name ?? 'Sistem / Tidak Diketahui' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeColor = match(strtoupper($log->action)) {
                                        'TAMBAH' => 'bg-emerald-50 text-emerald-700',
                                        'UBAH' => 'bg-blue-50 text-blue-700',
                                        'HAPUS' => 'bg-amber-50 text-amber-700',
                                        'PULIH ARSIP', 'PULIHKAN' => 'bg-teal-50 text-teal-700',
                                        'HAPUS PERMANEN' => 'bg-red-50 text-red-700',
                                        'ARSIP' => 'bg-amber-50 text-amber-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-slate-800 text-sm font-normal">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-slate-400">
                                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Belum ada catatan aktivitas yang terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>

    <!-- retention notice -->
    <div class="mt-4 text-xs text-slate-500">
        <p>Catatan: Log aktivitas sistem akan dihapus secara otomatis apabila usianya telah melebihi 30 hari.</p>
    </div>
@endsection
