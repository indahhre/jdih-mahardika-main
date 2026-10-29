@extends('layouts.admin')

@section('title', 'Dokumen')

@section('content')
    <!-- Toolbar Filter & Pencarian Admin -->
    <div class="mb-4 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.documents.index') }}" class="flex flex-col gap-3">
            
            <!-- 1. Baris Atas: Form Pencarian Dokumen -->
            <div class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dokumen…"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50 transition shrink-0 cursor-pointer">
                    Cari
                </button>
            </div>

            <!-- 2. Baris Bawah: Dropdown Filter & Tombol Tambah Dokumen -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                
                <!-- Filter Dropdown & Tombol Aksi Filter -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter Jenis Dokumen -->
                    <select name="jenis" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('jenis') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Tahun -->
                    <select name="tahun" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Status -->
                    <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Berlaku" {{ request('status') == 'Berlaku' ? 'selected' : '' }}>Berlaku</option>
                        <option value="Tidak Berlaku" {{ request('status') == 'Tidak Berlaku' ? 'selected' : '' }}>Tidak Berlaku</option>
                    </select>

                    <!-- Dropdown Urutan / Sorting -->
                    <select name="sort" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
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

                <!-- Tombol Tambah Dokumen -->
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

    <!-- Tabel Daftar Dokumen -->
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
                @foreach ($documents as $document)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 py-5 font-medium text-slate-900">{{ \Illuminate\Support\Str::limit($document->judul, 60) }}</td>
                        <td class="px-4 py-5 text-slate-500">
                            <!-- Badge Tipe Dokumen dengan padding yang lebih longgar (px-4 py-2) -->
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-700">
                                {{ $document->tipe_dokumen }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-slate-500">{{ $document->nomor }}</td>
                        <td class="px-4 py-5 text-slate-500">{{ $document->tahun }}</td>
                        <td class="px-4 py-5">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $document->status === 'Berlaku' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
                                {{ $document->status }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.documents.edit', $document) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition cursor-pointer shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Ubah
                                </a>
                                @if (auth()->user()->isSuperAdmin())
                                    <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition cursor-pointer shadow-sm">
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

    <div class="mt-4">{{ $documents->links() }}</div>
@endsection