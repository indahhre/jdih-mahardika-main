@extends('layouts.admin')

@section('title', 'Log Aktivitas Admin')

@section('content')
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs text-slate-500 mt-0.5">Memantau seluruh rekam jejak tindakan dan operasional admin.</p>
        </div>

        <!-- Form Pencarian Log -->
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas, admin, atau aksi..."
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:ring-blue-500 w-64">
            <button type="submit" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-900 transition cursor-pointer">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('admin.activity-logs.index') }}" class="text-xs text-slate-500 hover:underline cursor-pointer">Reset</a>
            @endif
        </form>
    </div>

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
                                    'PULIHKAN' => 'bg-teal-50 text-teal-700',
                                    'HAPUS PERMANEN' => 'bg-red-50 text-red-700',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                            @endphp
                            <!-- Badge Modern & Clean dengan soft background tanpa border kaku -->
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <!-- Keterangan detail diperjelas agar nyaman dibaca -->
                        <td class="px-4 py-3 text-slate-800 text-sm font-normal">
                            {{ $log->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">
                            Belum ada catatan aktivitas yang terekam.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>

    <!-- Catatan Informasi Formal -->
    <div class="mt-4 text-xs text-slate-500">
        <p>Catatan: Log aktivitas sistem akan dihapus secara otomatis apabila usianya telah melebihi 30 hari.</p>
    </div>
@endsection