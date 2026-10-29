<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('tipe_dokumen');
            $table->text('judul');
            $table->string('nomor');
            $table->unsignedSmallInteger('tahun');
            $table->enum('status', ['Berlaku', 'Tidak Berlaku'])->default('Berlaku');

            // Tanggal dipisah jelas sesuai istilah hukum -- ini yang bikin situs lama
            // salah tampil (frontend baca kolom yang tidak pernah ada di database).
            $table->date('tanggal_penetapan')->nullable(); // tanggal SK ditandatangani
            $table->date('tanggal_pengundangan')->nullable(); // tanggal diumumkan/mulai berlaku
            $table->date('tanggal_berakhir')->nullable();

            $table->string('tempat_penetapan')->nullable();
            $table->string('penandatangan')->nullable();
            $table->string('sumber')->nullable();
            $table->string('subjek')->nullable();
            $table->string('bidang')->nullable();
            $table->string('bahasa')->default('Indonesia');

            $table->text('ringkasan')->nullable();
            $table->json('tags')->nullable();

            // Relasi dokumen sungguhan (FK), bukan teks bebas seperti situs lama.
            $table->foreignId('mencabut_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('dicabut_dengan_id')->nullable()->constrained('documents')->nullOnDelete();
            // Cadangan teks untuk kasus dokumen yang dirujuk tidak ada di database ini
            // (mis. mencabut peraturan dari instansi luar UIM).
            $table->string('mencabut_teks')->nullable();
            $table->string('dicabut_dengan_teks')->nullable();

            // Berkas: R2/S3 sebagai sumber utama nantinya, Drive sebagai cadangan/sumber asli.
            $table->string('file_url')->nullable();
            $table->string('gdrive_url')->nullable();

            $table->string('legacy_source_id')->nullable()->comment('id dokumen di jdih.mahardika.ac.id lama, untuk audit migrasi');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tipe_dokumen']);
            $table->index(['tahun']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
