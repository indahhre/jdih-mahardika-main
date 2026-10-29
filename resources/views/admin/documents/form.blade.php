@extends('layouts.admin')

@section('title', $document->exists ? 'Ubah Dokumen' : 'Tambah Dokumen')

@section('content')

    <form method="POST" action="{{ $document->exists ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="max-w-3xl space-y-6" enctype="multipart/form-data">
        @csrf
        @if ($document->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div id="duplicate-banner" style="display: none;" class="rounded-lg border border-red-400 bg-red-50 p-4 text-red-700 shadow-sm" role="alert">
            <div class="flex items-start gap-3">
                <svg class="h-6 w-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h4 class="font-bold text-red-800">⚠️ Peringatan Keras: Pengunggahan Ditolak!</h4>
                    <p id="duplicate-message" class="mt-1 text-sm leading-relaxed"></p>
                </div>
            </div>
        </div>


        <div class="grid gap-4 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Judul</label>
                <input type="text" name="judul" id="judul" value="{{ old('judul', $document->judul) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tipe Dokumen</label>
                <select name="tipe_dokumen" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 bg-white focus:border-blue-500 focus:outline-none">
                    <option value="" disabled {{ old('tipe_dokumen', $document->tipe_dokumen) ? '' : 'selected' }}>-- Pilih Tipe Dokumen --</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->name }}" @selected(old('tipe_dokumen', $document->tipe_dokumen) === $type->name)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nomor</label>
                <input type="text" name="nomor" id="nomor" value="{{ old('nomor', $document->nomor) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $document->tahun) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                <select name="status" data-status-select class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="Berlaku" @selected(old('status', $document->status) === 'Berlaku')>Berlaku</option>
                    <option value="Tidak Berlaku" @selected(old('status', $document->status) === 'Tidak Berlaku')>Tidak Berlaku</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tempat Penetapan</label>
                <input type="text" name="tempat_penetapan" value="{{ old('tempat_penetapan', $document->tempat_penetapan) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Penetapan</label>
                <input type="date" name="tanggal_penetapan" value="{{ old('tanggal_penetapan', $document->tanggal_penetapan?->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Pengundangan</label>
                <input type="date" name="tanggal_pengundangan" value="{{ old('tanggal_pengundangan', $document->tanggal_pengundangan?->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Berakhir</label>
                <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', $document->tanggal_berakhir?->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Penandatangan</label>
                <input type="text" name="penandatangan" value="{{ old('penandatangan', $document->penandatangan) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Sumber</label>
                <input type="text" name="sumber" value="{{ old('sumber', $document->sumber) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Subjek</label>
                <input type="text" name="subjek" value="{{ old('subjek', $document->subjek) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Bidang</label>
                <input type="text" name="bidang" value="{{ old('bidang', $document->bidang) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Ringkasan</label>
                <textarea name="ringkasan" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('ringkasan', $document->ringkasan) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Tags (pisahkan dengan koma)</label>
                <input type="text" name="tags" id="tags" value="{{ old('tags', is_array($document->tags) ? implode(', ', $document->tags) : '') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Link Google Drive</label>
                <input type="url" name="gdrive_url" value="{{ old('gdrive_url', $document->gdrive_url) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

    
            <div class="md:col-span-2">
                <label for="file_dokumen" class="mb-1 block text-sm font-medium text-slate-700">Berkas Dokumen (PDF/DOC/DOCX)</label>
                <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.doc,.docx"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                @error('file_dokumen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="grid gap-4 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Dokumen ini mencabut <span class="font-normal text-slate-400">(opsional)</span></label>
                @include('admin.documents._searchable-document-select', [
                    'name' => 'mencabut_id',
                    'selected' => $documentOptions->firstWhere('id', (int) old('mencabut_id', $document->mencabut_id)),
                    'documentOptions' => $documentOptions,
                ])
            </div>
            <div data-dicabut-dengan-field>
                <label class="mb-1 block text-sm font-medium text-slate-700">Dokumen ini dicabut dengan</label>
                @include('admin.documents._searchable-document-select', [
                    'name' => 'dicabut_dengan_id',
                    'selected' => $documentOptions->firstWhere('id', (int) old('dicabut_dengan_id', $document->dicabut_dengan_id)),
                    'documentOptions' => $documentOptions,
                ])
                <p class="mt-1 text-xs text-slate-400">Hanya berlaku kalau status dokumen ini "Tidak Berlaku".</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" id="btn-simpan" class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-medium text-white hover:bg-blue-800 transition-all">Simpan</button>
            <a href="{{ route('admin.documents.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm text-slate-600 hover:bg-slate-50">Batal</a>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('file_dokumen');
        const nomorInput = document.getElementById('nomor');
        const btnSimpan = document.getElementById('btn-simpan');
        const banner = document.getElementById('duplicate-banner');
        const messageText = document.getElementById('duplicate-message');
        
        const documentId = "{{ $document->exists ? $document->id : '' }}";

        // format judul ke Title Case saat user tekan Enter
        const inputJudul = document.getElementById('judul');

        function formatJudulToTitleCase() {
            let str = inputJudul.value.trim().toLowerCase();
            if (!str) return;

            let formattedStr = str.replace(/(?:^|\s|-|\/)\S/g, function (m) {
                return m.toUpperCase();
            });

            const kataKecil = ['Dan', 'Atau', 'Tentang', 'Di', 'Ke', 'Dari', 'Yang', 'Untuk', 'Pada'];
            kataKecil.forEach(kata => {
                const regex = new RegExp('\\b' + kata + '\\b', 'g');
                formattedStr = formattedStr.replace(regex, kata.toLowerCase());
            });

            formattedStr = formattedStr.charAt(0).toUpperCase() + formattedStr.slice(1);
            inputJudul.value = formattedStr;
        }

        if (inputJudul) {
            inputJudul.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // cegah submit saat Enter di field judul
                    formatJudulToTitleCase();
                }
            });
        }

        function checkDuplicate() {
            const formData = new FormData();

            if (fileInput && fileInput.files.length > 0) {
                formData.append('file_dokumen', fileInput.files[0]);
            }
            if (nomorInput && nomorInput.value.trim() !== '') {
                formData.append('nomor', nomorInput.value.trim());
            }
            if (documentId) {
                formData.append('document_id', documentId);
            }

            formData.append('_token', '{{ csrf_token() }}');

            if ((!fileInput || fileInput.files.length === 0) && (!nomorInput || nomorInput.value.trim() === '')) {
                hideBanner();
                return;
            }

            fetch('{{ route("admin.documents.check-duplicate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.is_blocked) {
                    banner.style.display = 'block';
                    messageText.innerText = data.message;
                    if (btnSimpan) {
                        btnSimpan.disabled = true;
                        btnSimpan.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    hideBanner();
                }
            })
            .catch(error => console.error('Error Live Check:', error));
        }

        function hideBanner() {
            if (banner) banner.style.display = 'none';
            if (messageText) messageText.innerText = '';
            if (btnSimpan) {
                btnSimpan.disabled = false;
                btnSimpan.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        if (fileInput) {
            fileInput.addEventListener('change', checkDuplicate);
        }

        if (nomorInput) {
            let debounceTimeout = null;
            nomorInput.addEventListener('input', function () {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(checkDuplicate, 400);
            });
        }
    });
    </script>
@endsection