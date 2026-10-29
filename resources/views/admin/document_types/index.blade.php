@extends('layouts.admin')

@section('title', 'Kelola Jenis Dokumen')

@section('content')
    <div class="flex items-center justify-between">
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- tambah jenis dokumen -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="mb-3 text-sm font-semibold text-slate-700">Tambah Jenis Dokumen Baru</h3>
        <form action="{{ route('admin.document-types.store') }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Peraturan Rektor..." required
                class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-medium text-white hover:bg-blue-800 transition-all cursor-pointer shadow-sm">
                Simpan
            </button>
        </form>
    </div>

    <!-- tabel jenis dokumen -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700">
                <tr>
                    <th class="p-4 font-semibold w-16">No</th>
                    <th class="p-4 font-semibold">Nama Jenis Dokumen</th>
                    <th class="p-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
                @forelse ($types as $index => $type)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4">{{ $types->firstItem() + $index }}</td>
                        <td class="p-4 font-medium text-slate-800">{{ $type->name }}</td>
                        <td class="p-4 text-right">
                            <form action="{{ route('admin.document-types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jenis dokumen ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition cursor-pointer shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-6 text-center text-slate-400 italic">Belum ada jenis dokumen yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">
            {{ $types->links() }}
        </div>
    </div>
</div>
@endsection