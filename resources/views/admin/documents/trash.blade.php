@extends('layouts.admin')

@section('title', 'Arsip Dokumen')

@section('content')
    <div class="mb-4 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Arsip Dokumen</h1>
                <p class="text-xs text-slate-500">Daftar dokumen yang telah diarsipkan dan disembunyikan dari katalog publik.</p>
            </div>
            <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                ← Kembali ke Dokumen Aktif
            </a>
        </div>

        <form method="GET" action="{{ route('admin.documents.archived') }}" data-live-search-form data-target="#admin-archived-container" class="pt-2 border-t border-slate-100">
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dokumen di arsip…"
                        autocomplete="off"
                        data-live-search-input
                        class="w-full rounded-lg border border-slate-300 bg-slate-50/50 py-2 pl-9 pr-14 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 gap-1.5">
                        <div data-live-search-spinner class="hidden animate-spin text-blue-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </div>
                        <button type="button" data-live-search-clear class="{{ request('q') ? '' : 'hidden' }} rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition cursor-pointer" title="Hapus pencarian">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50 transition shrink-0 cursor-pointer">
                    Cari
                </button>
            </div>
        </form>
    </div>

    @if(session('status'))
        <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <div id="admin-archived-container">
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4">Judul</th>
                        <th class="px-4 py-4">Tipe</th>
                        <th class="px-4 py-4">Nomor</th>
                        <th class="px-4 py-4">Tahun</th>
                        <th class="px-4 py-4">Tanggal Diarsipkan</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($documents as $document)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-5 font-medium text-slate-900">{{ \Illuminate\Support\Str::limit($document->judul, 60) }}</td>
                            <td class="px-4 py-5 text-slate-500">
                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                    {{ $document->tipe_dokumen }}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-slate-500">{{ $document->nomor }}</td>
                            <td class="px-4 py-5 text-slate-500">{{ $document->tahun }}</td>
                            <td class="px-4 py-5 text-slate-500 text-xs">
                                {{ optional($document->archived_at ?? $document->deleted_at)->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <form action="{{ route('admin.documents.restore-archived', $document->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin memulihkan dokumen ini kembali aktif?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition cursor-pointer shadow-sm">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Pulihkan
                                        </button>
                                    </form>

                                    @if (auth()->user()->isSuperAdmin())
                                        <form action="{{ route('admin.documents.force-delete', $document->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini secara permanen? Data tidak dapat dikembalikan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition cursor-pointer shadow-sm">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus Permanen
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Belum ada dokumen yang diarsipkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $documents->links() }}</div>
    </div>
@endsection
