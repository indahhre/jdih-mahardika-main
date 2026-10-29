<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'editor'])->default('editor')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('google_id')->nullable()->unique()->after('is_active');
            $table->string('picture_url')->nullable()->after('google_id');
            $table->timestamp('last_login_at')->nullable()->after('picture_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'google_id', 'picture_url', 'last_login_at']);
        });
    }
};
