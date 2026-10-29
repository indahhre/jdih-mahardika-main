<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Simplify the "Jenis Dokumen" taxonomy from 11 categories down to 5,
     * grouped by document nature rather than issuing office (the issuer
     * is still available via the `sumber` field / document number).
     */
    private const MAP = [
        'SK Rektor' => 'Keputusan',
        'SK Dekan' => 'Keputusan',
        'SK Yayasan' => 'Keputusan',
        'Peraturan Daerah' => 'Peraturan',
        'Peraturan Walikota' => 'Peraturan',
        'Pedoman' => 'Pedoman',
        'Panduan' => 'Pedoman',
        'Standar SPMI' => 'Dokumen SPMI',
        'Kebijakan SPMI' => 'Dokumen SPMI',
        'Formulir SPMI' => 'Dokumen SPMI',
        // 'Lainnya' stays as-is.
    ];

    public function up(): void
    {
        foreach (self::MAP as $old => $new) {
            DB::table('documents')->where('tipe_dokumen', $old)->update(['tipe_dokumen' => $new]);
        }
    }

    /**
     * Not reversible: several old categories collapse into one new category
     * (e.g. SK Rektor/Dekan/Yayasan all become "Keputusan"), so there is no
     * way to recover which specific old label each row originally had.
     */
    public function down(): void
    {
        //
    }
};
