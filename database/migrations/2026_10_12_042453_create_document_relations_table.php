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
        Schema::create('document_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('target_document_id')->constrained('documents')->onDelete('cascade');
            $table->enum('tipe_relasi', ['Mencabut', 'Mengubah']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_relations');
    }
};