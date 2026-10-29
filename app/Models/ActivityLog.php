<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class ActivityLog extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    /**
     * Relasi ke User (admin yang melakukan aksi).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kriteria record log yang akan di-prune (lebih dari 30 hari).
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(30));
    }
}