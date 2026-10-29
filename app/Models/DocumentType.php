<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'name',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_type_id');
    }
}