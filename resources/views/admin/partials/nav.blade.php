{{-- Shared admin nav links, included from both the desktop sidebar and the
     mobile dropdown in layouts/admin.blade.php. Kept as a real partial
     (not an inline @php closure) so Blade directives inside it actually
     compile -- see git history for the bug that happened when this lived
     inside an @php ... @endphp block. --}}

@php
    $isDokumenActive = request()->routeIs('admin.documents.index') || request()->routeIs('admin.documents.create') || request()->routeIs('admin.documents.edit');
@endphp
<a href="{{ route('admin.documents.index') }}" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer {{ $isDokumenActive ? 'bg-blue-900 text-white' : '' }}">
    Dokumen
</a>

@if (auth()->user()->isSuperAdmin())
    @php
        $isKelolaDokumenActive = request()->routeIs('admin.document-types.*');
    @endphp
    <a href="{{ route('admin.document-types.index') }}" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer {{ $isKelolaDokumenActive ? 'bg-blue-900 text-white' : '' }}">
        Kelola Dokumen
    </a>
@endif

<a href="{{ route('admin.documents.trash') }}" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer {{ request()->routeIs('admin.documents.trash') ? 'bg-blue-900 text-white' : '' }}">
    Arsip Dokumen
</a>
<a href="{{ route('admin.activity-logs.index') }}" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer {{ request()->routeIs('admin.activity-logs.*') ? 'bg-blue-900 text-white' : '' }}">
    Log Aktivitas
</a>
<a href="{{ route('admin.account.edit') }}" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer {{ request()->routeIs('admin.account.*') ? 'bg-blue-900 text-white' : '' }}">
    Pengaturan Akun
</a>
@if (auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-2.5 hover:bg-blue-900 cursor-pointer {{ request()->routeIs('admin.users.*') ? 'bg-blue-900 text-white' : '' }}">
        Kelola Pengguna
    </a>
@endif