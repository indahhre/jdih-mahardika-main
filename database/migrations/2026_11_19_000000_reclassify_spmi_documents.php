<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pindahkan dokumen di dalam "Dokumen SPMI" yang merupakan Peraturan ke "Peraturan"
        DB::table('documents')
            ->whereIn('id', [7, 8, 9])
            ->update(['tipe_dokumen' => 'Peraturan']);

        // 2. Pindahkan Diagram SOTK (#106) dan dokumen umum (#156) dari "Dokumen SPMI" ke "Dokumen Lainnya"
        DB::table('documents')
            ->whereIn('id', [106, 156])
            ->update(['tipe_dokumen' => 'Dokumen Lainnya']);

        // 3. Pindahkan dokumen Prosedur / SOP (#73, #154) dari "Dokumen SPMI" ke "Standar Operasional Prosedur (SOP)"
        DB::table('documents')
            ->whereIn('id', [73, 154])
            ->update(['tipe_dokumen' => 'Standar Operasional Prosedur (SOP)']);

        // 4. Pindahkan SK Tim Audit (#161) dari "Dokumen SPMI" ke "Surat Keputusan"
        DB::table('documents')
            ->where('id', 161)
            ->update(['tipe_dokumen' => 'Surat Keputusan']);

        // 5. Masukkan dokumen murni 4 Pilar SPMI (Kebijakan, Manual, Standar, Formulir SPMI)
        // yang masih berada di kategori lama ke dalam "Dokumen SPMI"
        DB::table('documents')
            ->whereIn('id', [128, 131, 141, 142])
            ->update(['tipe_dokumen' => 'Dokumen SPMI']);

        // Bersihkan kategori SPMI lama yang sudah kosong dari tabel master document_types
        DB::table('document_types')
            ->whereIn('name', ['Kebijakan Mutu SPMI', 'Manual Mutu SPMI'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

