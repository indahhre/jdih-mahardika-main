<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipe_dokumen', 'judul', 'nomor', 'tahun', 'status',
        'tanggal_penetapan', 'tanggal_pengundangan', 'tanggal_berakhir',
        'tempat_penetapan', 'penandatangan', 'sumber', 'subjek', 'bidang', 'bahasa',
        'ringkasan', 'tags',
        'mencabut_id', 'dicabut_dengan_id', 'mencabut_teks', 'dicabut_dengan_teks',
        'file_url', 'gdrive_url', 'legacy_source_id',
        'file_hash', 'file_name', 'file_size', 'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penetapan' => 'date',
            'tanggal_pengundangan' => 'date',
            'tanggal_berakhir' => 'date',
            'tags' => 'array',
        ];
    }

    /** The document this one revokes. */
    public function mencabut(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'mencabut_id');
    }

    /** The document that revoked this one. */
    public function dicabutDengan(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'dicabut_dengan_id');
    }

    /** Documents that list this one as revoked-by. */
    public function dicabutOleh(): HasMany
    {
        return $this->hasMany(Document::class, 'dicabut_dengan_id');
    }

    /** Documents that this one lists as having revoked. */
    public function mencabutDokumenLain(): HasMany
    {
        return $this->hasMany(Document::class, 'mencabut_id');
    }

    /**
     * TAMBAHAN BARU: Relasi many-to-many via tabel document_relations
     * Dokumen baru yang mencabut dokumen lama
     */
    public function mencabutDokumenRelasi(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_relations', 'source_document_id', 'target_document_id')
                    ->withPivot('tipe_relasi')
                    ->withTimestamps();
    }

    /**
     * TAMBAHAN BARU: Relasi many-to-many via tabel document_relations
     * Dokumen lama yang dicabut oleh dokumen baru
     */
    public function dicabutOlehRelasi(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_relations', 'target_document_id', 'source_document_id')
                    ->withPivot('tipe_relasi')
                    ->withTimestamps();
    }

    /**
     * Embeddable Google Drive preview URL, converted from a normal
     * "view" share link (https://drive.google.com/file/d/{id}/view...).
     * Null when gdrive_url isn't a recognizable Drive file link (e.g. the
     * handful of placeholder/test URLs left over from the legacy data).
     */
    protected function gdrivePreviewUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->gdrive_url) {
                return null;
            }

            if (! preg_match('#/file/d/([\w-]+)#', $this->gdrive_url, $matches)) {
                return null;
            }

            return "https://drive.google.com/file/d/{$matches[1]}/preview";
        });
    }

    /** Relasi ke User (Admin) yang mengunggah dokumen */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}