@extends('layouts.admin')

@section('title', 'Dokumen')

@section('content')
    <!-- filter & search toolbar -->
    <div class="mb-4 bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.documents.index') }}" data-live-search-form data-target="#admin-documents-container" class="flex flex-col gap-3">
            
            <!-- search input with live search indicator -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau nomor dokumen…"
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

            <!-- filters & actions -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <select name="jenis" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('jenis') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    <select name="tahun" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Berlaku" {{ request('status') == 'Berlaku' ? 'selected' : '' }}>Berlaku</option>
                        <option value="Tidak Berlaku" {{ request('status') == 'Tidak Berlaku' ? 'selected' : '' }}>Tidak Berlaku</option>
                    </select>

                    <select name="sort" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Upload Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul (A-Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul (Z-A)</option>
                    </select>

                    @if(request()->anyFilled(['q', 'jenis', 'tahun', 'status', 'sort']))
                        <a href="{{ route('admin.documents.index') }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-700 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-800 transition whitespace-nowrap cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Dokumen
                    </a>
                </div>
            </div>

        </form>
    </div>

    <!-- document table container for live replacement -->
    <div id="admin-documents-container">
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4">Judul</th>
                        <th class="px-4 py-4">Tipe</th>
                        <th class="px-4 py-4">Nomor</th>
                        <th class="px-4 py-4">Tahun</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($documents as $document)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-5 font-medium text-slate-900">{{ \Illuminate\Support\Str::limit($document->judul, 60) }}</td>
                            <td class="px-4 py-5 text-slate-500">
                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-700">
                                    {{ $document->tipe_dokumen }}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-slate-500">{{ $document->nomor }}</td>
                            <td class="px-4 py-5 text-slate-500">{{ $document->tahun }}</td>
                            <td class="px-4 py-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $document->status === 'Berlaku' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $document->status === 'Berlaku' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $document->status }}
                                </span>
                                @if ($document->status === 'Tidak Berlaku')
                                    @php
                                        $revoker = $document->dicabutDengan ?? $document->dicabutOlehRelasi->first();
                                    @endphp
                                    @if ($revoker)
                                        <div class="mt-1 text-[11px] text-rose-700 font-medium max-w-[200px] truncate" title="Dicabut oleh: {{ $revoker->nomor }} - {{ $revoker->judul }}">
                                            Dicabut: <span class="font-semibold">{{ $revoker->nomor }}</span>
                                        </div>
                                    @elseif ($document->dicabut_dengan_teks)
                                        <div class="mt-1 text-[11px] text-rose-700 font-medium max-w-[200px] truncate" title="{{ $document->dicabut_dengan_teks }}">
                                            Dicabut: {{ $document->dicabut_dengan_teks }}
                                        </div>
                                    @endif
                                @else
                                    @php
                                        $revoked = $document->mencabut ?? $document->mencabutDokumenRelasi->first();
                                    @endphp
                                    @if ($revoked)
                                        <div class="mt-1 text-[11px] text-emerald-700 font-medium max-w-[200px] truncate" title="Mencabut: {{ $revoked->nomor }} - {{ $revoked->judul }}">
                                            Mencabut: <span class="font-semibold">{{ $revoked->nomor }}</span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.documents.edit', $document) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition cursor-pointer shadow-sm">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Ubah
                                    </a>

                                    <form action="{{ route('admin.documents.archive', $document) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin mengarsipkan dokumen ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition cursor-pointer shadow-sm">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                            </svg>
                                            Arsipkan
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tidak ditemukan dokumen yang cocok dengan kata kunci pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $documents->links() }}</div>
    </div>
@endsection
