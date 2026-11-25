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
        // Daftar pasangan dokumen: [source_id (dokumen baru / mencabut), target_id (dokumen lama / dicabut)]
        $revocationPairs = [
            // 1. Standar SPMI 2024 mencabut Standar SPMI 2023
            ['source' => 63, 'target' => 62],
            // 2. Manual SPMI 2024 mencabut Manual SPMI 2023
            ['source' => 66, 'target' => 65],
            // 3. Kebijakan SPMI 2024 mencabut Kebijakan Mutu SPMI 2023
            ['source' => 64, 'target' => 129],
            // 4. SK Pengangkatan Pengelola Penjaminan Mutu 2024-2028 mencabut SK Pengelola LPM 2023
            ['source' => 42, 'target' => 28],
            // 5. SK Tata Tertib Mahasiswa mencabut SK Kewajiban Olahraga Civitas
            ['source' => 38, 'target' => 33],
            // 6. SK Rektor AMI mencabut SK Rektor Tentang Tim AMI
            ['source' => 165, 'target' => 158],
        ];

        foreach ($revocationPairs as $pair) {
            $sourceId = $pair['source'];
            $targetId = $pair['target'];

            // Pastikan kedua dokumen ada di tabel
            $sourceExists = DB::table('documents')->where('id', $sourceId)->exists();
            $targetExists = DB::table('documents')->where('id', $targetId)->exists();

            if ($sourceExists && $targetExists) {
                // Dokumen baru berstatus Berlaku dan mencabut dokumen lama
                DB::table('documents')->where('id', $sourceId)->update([
                    'status' => 'Berlaku',
                    'mencabut_id' => $targetId,
                ]);

                // Dokumen lama berstatus Tidak Berlaku dan dicabut oleh dokumen baru
                DB::table('documents')->where('id', $targetId)->update([
                    'status' => 'Tidak Berlaku',
                    'dicabut_dengan_id' => $sourceId,
                ]);

                // Sinkronkan ke tabel pivot document_relations
                $relationExists = DB::table('document_relations')
                    ->where('source_document_id', $sourceId)
                    ->where('target_document_id', $targetId)
                    ->exists();

                if (! $relationExists) {
                    DB::table('document_relations')->insert([
                        'source_document_id' => $sourceId,
                        'target_document_id' => $targetId,
                        'tipe_relasi' => 'Mencabut',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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

