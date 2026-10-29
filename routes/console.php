<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- TAMBAHAN
use App\Models\ActivityLog; // <-- TAMBAHAN

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- TAMBAHAN: Jadwal pembersihan otomatis log aktivitas > 30 hari ---
Schedule::command('model:prune', [
    '--model' => [ActivityLog::class],
])->daily();