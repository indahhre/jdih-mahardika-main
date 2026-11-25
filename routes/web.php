<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/dokumen/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/tentang', [FaqController::class, 'index'])->name('faq');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::redirect('/', '/admin/dokumen');

    Route::post('/dokumen/check-duplicate', [AdminDocumentController::class, 'checkDuplicate'])->name('documents.check-duplicate');

    // --- Route Arsip Dokumen & Pemulihan ---
    Route::get('dokumen/arsip', [AdminDocumentController::class, 'archivedIndex'])->name('documents.trash');
    Route::get('dokumen/arsip-dokumen', [AdminDocumentController::class, 'archivedIndex'])->name('documents.archived');
    Route::match(['patch', 'post', 'put'], 'dokumen/{id}/restore', [AdminDocumentController::class, 'restoreArchived'])->name('documents.restore');
    Route::match(['patch', 'post', 'put'], 'dokumen/{id}/restore-archived', [AdminDocumentController::class, 'restoreArchived'])->name('documents.restore-archived');
    Route::delete('dokumen/{id}/force-delete', [AdminDocumentController::class, 'forceDelete'])->name('documents.force-delete');
    Route::patch('dokumen/{id}/archive', [AdminDocumentController::class, 'archive'])->name('documents.archive');

    // --- Route Log Aktivitas ---
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::resource('dokumen', AdminDocumentController::class)->parameters(['dokumen' => 'document'])->names('documents');

    Route::get('/akun', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [AccountController::class, 'update'])->name('account.update');

    Route::middleware('admin:super_admin')->group(function () {
        Route::resource('pengguna', UserController::class)->parameters(['pengguna' => 'user'])->names('users');
        
        // --- Route Kelola Jenis Dokumen (Khusus Super Admin) ---
        Route::resource('document-types', DocumentTypeController::class)->except(['show', 'edit', 'update']);
    });
});