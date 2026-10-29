<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Seed documents migrated from the legacy jdih.mahardika.ac.id site.
     * See scripts/migrate-legacy.mjs in the old `jdih` project for how this
     * JSON was produced (field renames + resolving mencabut/dicabut_dengan
     * free text into real foreign keys by matching document numbers).
     */
    public function run(): void
    {
        $path = __DIR__.'/legacy-documents.json';
        $records = json_decode(file_get_contents($path), true);

        // Explicit ids preserved from the migration script so mencabut_id /
        // dicabut_dengan_id foreign keys (also assigned there) line up.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('documents')->delete();

        $now = now();

        foreach ($records as $record) {
            DB::table('documents')->insert([
                'id' => $record['id'],
                'tipe_dokumen' => $record['tipeDokumen'],
                'judul' => $record['judul'],
                'nomor' => $record['nomor'],
                'tahun' => $record['tahun'],
                'status' => $record['status'],
                'tanggal_penetapan' => $record['tanggalPenetapan'],
                'tanggal_pengundangan' => $record['tanggalPengundangan'],
                'tanggal_berakhir' => $record['tanggalBerakhir'],
                'tempat_penetapan' => $record['tempatPenetapan'],
                'penandatangan' => $record['penandatangan'],
                'sumber' => $record['sumber'],
                'subjek' => $record['subjek'],
                'bidang' => $record['bidang'],
                'bahasa' => $record['bahasa'],
                'ringkasan' => $record['ringkasan'],
                'tags' => json_encode($record['tagsJson'] ? json_decode($record['tagsJson'], true) : []),
                'mencabut_id' => $record['mencabutId'],
                'dicabut_dengan_id' => $record['dicabutDenganId'],
                'mencabut_teks' => $record['mencabutTeks'],
                'dicabut_dengan_teks' => $record['dicabutDenganTeks'],
                'file_url' => $record['fileUrl'],
                'gdrive_url' => $record['gdriveUrl'],
                'legacy_source_id' => $record['legacySourceId'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info(count($records).' dokumen berhasil di-seed dari data situs lama.');
    }
}
