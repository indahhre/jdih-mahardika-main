<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Menambahkan kolom hash dan metadata berkas
            $table->string('file_hash', 64)->nullable()->index();
            $table->string('file_name', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            
            // Menambahkan ID admin yang mengunggah (relasi ke tabel users)
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu sebelum menghapus kolom
            $table->dropForeign(['created_by_id']);
            
            // Hapus kolom-kolom yang ditambahkan di method up()
            $table->dropColumn(['file_hash', 'file_name', 'file_size', 'created_by_id']);
        });
    }
};