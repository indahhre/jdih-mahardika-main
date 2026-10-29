{{--
    Searchable document combobox. Expects:
    - $name: form field name for the hidden id input
    - $selected: currently selected Document|null
    - $documentOptions: collection of candidate documents (id, nomor, judul)
--}}
<div class="relative" data-searchable-select>
    <input type="hidden" name="{{ $name }}" value="{{ old($name, $selected?->id) }}" data-search-value>
    <input
        type="text"
        autocomplete="off"
        placeholder="Ketik nomor atau judul untuk mencari…"
        value="{{ $selected ? $selected->nomor.' — '.\Illuminate\Support\Str::limit($selected->judul, 50) : '' }}"
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        data-search-input
    >
    <div class="absolute left-0 right-0 top-full z-10 mt-1 hidden max-h-56 overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-lg" data-search-options>
        <button type="button" data-value="" data-label="— Tidak ada —" class="block w-full rounded-md px-3 py-2 text-left text-sm text-slate-500 hover:bg-slate-100">
            — Tidak ada —
        </button>
        @foreach ($documentOptions as $option)
            <button type="button" data-value="{{ $option->id }}" data-label="{{ $option->nomor }} — {{ \Illuminate\Support\Str::limit($option->judul, 50) }}" class="block w-full rounded-md px-3 py-2 text-left text-sm hover:bg-slate-100">
                {{ $option->nomor }} — {{ \Illuminate\Support\Str::limit($option->judul, 50) }}
            </button>
        @endforeach
    </div>
</div>
