<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable; // <-- TAMBAHAN: Import trait Prunable

class ActivityLog extends Model
{
    use HasFactory, Prunable; // <-- TAMBAHAN: Menyertakan trait Prunable di sini

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    /**
     * Relasi ke User (Admin yang melakukan aksi)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * TAMBAHAN: Tentukan kriteria data log yang akan dihapus otomatis (lebih dari 30 hari)
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(30));
    }
}