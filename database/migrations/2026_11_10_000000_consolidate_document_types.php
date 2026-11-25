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
        // 1. Dokumen SPMI consolidation
        // Seluruh isi dokumen jenis Standar Mutu SPMI dimasukkan ke Dokumen SPMI
        DB::table('documents')
            ->where('tipe_dokumen', 'Standar Mutu SPMI')
            ->update(['tipe_dokumen' => 'Dokumen SPMI']);

        // 2. Surat Keputusan consolidation
        // Seluruh isi dokumen surat keputusan yayasan, rektor, dekan, dan keputusan lainnya
        // dijadikan satu ke jenis dokumen "Surat Keputusan"
        DB::table('documents')
            ->whereIn('tipe_dokumen', [
                'Surat Keputusan (SK) Yayasan',
                'Surat Keputusan (SK) Rektor',
                'Surat Keputusan (SK) Dekan',
                'Keputusan Lainnya',
                'Keputusan',
            ])
            ->update(['tipe_dokumen' => 'Surat Keputusan']);

        // 3. Peraturan consolidation
        // Seluruh isi dokumen Peraturan yayasan, rektor, dan peraturan lainnya
        // dimasukkan ke jenis dokumen "Peraturan"
        DB::table('documents')
            ->whereIn('tipe_dokumen', [
                'Peraturan Yayasan',
                'Peraturan Rektor',
                'Peraturan Lainnya',
                'Peraturan Institusi / Rektor',
            ])
            ->update(['tipe_dokumen' => 'Peraturan']);

        // 4. Sinkronkan tabel document_types
        $targetTypes = [
            'Dokumen SPMI',
            'Surat Keputusan',
            'Peraturan',
        ];

        foreach ($targetTypes as $typeName) {
            if (!DB::table('document_types')->where('name', $typeName)->exists()) {
                DB::table('document_types')->insert([
                    'name' => $typeName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Hapus tipe lama yang sudah dimerge dari tabel document_types
        $mergedOldTypes = [
            'Standar Mutu SPMI',
            'Surat Keputusan (SK) Yayasan',
            'Surat Keputusan (SK) Rektor',
            'Surat Keputusan (SK) Dekan',
            'Keputusan Lainnya',
            'Keputusan',
            'Peraturan Yayasan',
            'Peraturan Rektor',
            'Peraturan Lainnya',
            'Peraturan Institusi / Rektor',
        ];

        DB::table('document_types')->whereIn('name', $mergedOldTypes)->delete();

        // Pastikan semua tipe dokumen yang masih ada di tabel documents terdaftar di document_types
        // agar validasi form exists:document_types,name berjalan lancar
        $existingDocTypes = DB::table('documents')
            ->select('tipe_dokumen')
            ->distinct()
            ->pluck('tipe_dokumen');

        foreach ($existingDocTypes as $dt) {
            if ($dt && !DB::table('document_types')->where('name', $dt)->exists()) {
                DB::table('document_types')->insert([
                    'name' => $dt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

