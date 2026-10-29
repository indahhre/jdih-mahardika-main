<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;

class DocumentDuplicateChecker
{
    /**
     * Mengecek apakah ada dokumen yang sama persis berdasarkan file fisik atau nomor.
     */
    public function check(array $data, ?int $excludeDocumentId = null): array
    {
        $query = Document::with('createdBy');

        // Ambil objek file (mendukung kunci 'file' atau 'file_dokumen')
        $uploadedFile = $data['file'] ?? $data['file_dokumen'] ?? null;

        // 1. Cek File Hash (Isi Berkas Identik) menggunakan algoritma SHA-256
        if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
            $filePath = $uploadedFile->getRealPath();
            
            if ($filePath && file_exists($filePath)) {
                $hash = hash_file('sha256', $filePath);
                
                $duplicateFile = (clone $query)->where('file_hash', $hash);
                if ($excludeDocumentId) {
                    $duplicateFile->where('id', '!=', $excludeDocumentId);
                }
                $duplicateFile = $duplicateFile->first();

                if ($duplicateFile) {
                    return $this->formatResponse($duplicateFile, 'berkas yang sama persis');
                }
            }
        }

        // 2. Cek Nomor Dokumen Identik
        if (!empty($data['nomor'])) {
            $nomorClean = trim($data['nomor']);
            
            $duplicateNumber = (clone $query)->where('nomor', $nomorClean);
            if ($excludeDocumentId) {
                $duplicateNumber->where('id', '!=', $excludeDocumentId);
            }
            $duplicateNumber = $duplicateNumber->first();

            if ($duplicateNumber) {
                return $this->formatResponse($duplicateNumber, 'nomor dokumen yang sama');
            }
        }

        // Jika aman, kembalikan status false (tidak diblokir)
        return ['is_blocked' => false, 'message' => ''];
    }

    /**
     * Memformat pesan error agar informatif sesuai spesifikasi
     */
    private function formatResponse($document, $reason)
    {
        $admin = $document->createdBy ? $document->createdBy->name : 'Admin Sebelumnya';
        $tanggal = $document->created_at ? $document->created_at->format('d M Y H:i') : 'Tanggal Tidak Diketahui';

        return [
            'is_blocked' => true,
            'message' => "Dokumen ini tidak dapat diunggah karena {$reason} telah diunggah sebelumnya pada Dokumen: '{$document->judul}' (Nomor: {$document->nomor}) oleh {$admin} pada {$tanggal}. Sistem mencegah pengunggahan ganda."
        ];
    }
}