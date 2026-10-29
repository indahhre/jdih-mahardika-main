<?php

namespace App\Http\Controllers;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'q' => 'Apa itu JDIH?',
                'a' => 'JDIH (Jaringan Dokumentasi dan Informasi Hukum) adalah sistem informasi yang menyediakan akses terpusat terhadap seluruh peraturan dan dokumen hukum di lingkungan Universitas Insan Mahardika, mengikuti semangat Peraturan Presiden Nomor 33 Tahun 2012 tentang Jaringan Dokumentasi dan Informasi Hukum Nasional.',
            ],
            [
                'q' => 'Siapa saja yang bisa mengakses JDIH ini?',
                'a' => 'Seluruh dokumen bersifat terbuka dan dapat diakses oleh siapa saja tanpa perlu masuk/login — civitas akademika, mahasiswa, maupun masyarakat umum.',
            ],
            [
                'q' => 'Bagaimana cara mencari dokumen tertentu?',
                'a' => 'Gunakan kolom pencarian di beranda untuk mencari berdasarkan judul, nomor, atau subjek. Anda juga bisa menjelajah lewat menu "Jenis Dokumen" atau "Tahun" di navigasi, atau memakai filter pada halaman katalog dokumen.',
            ],
            [
                'q' => 'Apa arti status "Berlaku" dan "Tidak Berlaku"?',
                'a' => '"Berlaku" berarti dokumen tersebut masih sah dan mengikat. "Tidak Berlaku" berarti dokumen tersebut sudah dicabut atau digantikan oleh dokumen lain — biasanya tertaut pada bagian "Status Pencabutan" di halaman detail dokumen.',
            ],
            [
                'q' => 'Saya menemukan dokumen yang salah atau data yang perlu diperbarui, harus lapor ke mana?',
                'a' => 'Silakan hubungi pengelola JDIH melalui email uimakademik@mahardika.ac.id, sertakan judul/nomor dokumen dan detail perubahan yang dimaksud.',
            ],
            [
                'q' => 'Siapa yang mengelola konten JDIH ini?',
                'a' => 'Pengelolaan konten dilakukan oleh pengurus LP3M (Lembaga Pengembangan Pendidikan dan Penjaminan Mutu) Universitas Insan Mahardika bersama unit terkait di rektorat.',
            ],
        ];

        return view('faq.index', compact('faqs'));
    }
}
