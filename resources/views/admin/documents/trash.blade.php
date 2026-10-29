@extends('layouts.admin')

@section('title', 'Arsip Dokumen')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
        </div>

        <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition cursor-pointer">
            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Dokumen
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[720px] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3.5">Judul</th>
                    <th class="px-4 py-3.5">Tipe</th>
                    <th class="px-4 py-3.5">Nomor</th>
                    <th class="px-4 py-3.5">Tanggal Dihapus</th>
                    <th class="px-4 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($documents as $document)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 py-5 font-medium text-slate-900">
                            {{ $document->judul }}
                        </td>
                        <td class="px-4 py-5 text-slate-600 text-xs">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-700">
                                {{ $document->tipe_dokumen }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-slate-600 text-xs">
                            {{ $document->nomor ?? '-' }}
                        </td>
                        <td class="px-4 py-5 text-slate-500 text-xs whitespace-nowrap">
                            {{ $document->deleted_at ? $document->deleted_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-4 py-5 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">

                                <form action="{{ route('admin.documents.restore', $document->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition cursor-pointer shadow-sm">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Pulihkan
                                    </button>
                                </form>


                                <form action="{{ route('admin.documents.force-delete', $document->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini secara permanen? Data tidak dapat dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition cursor-pointer shadow-sm">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic">
                            Tidak ada arsip dokumen yang dihapus.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
@endsection