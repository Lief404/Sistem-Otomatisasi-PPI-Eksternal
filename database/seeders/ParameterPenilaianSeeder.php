<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParameterPenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // PRESENTASI (Mutu Presentasi)
            [
                'jenis' => 'presentasi',
                'sub_kategori' => 'Teknik Presentasi',
                'indikator' => [
                    'Persiapan',
                    'Sistematika Penyajian',
                    'Penggunaan Alat',
                    'Bahasa Lisan'
                ]
            ],
            [
                'jenis' => 'presentasi',
                'sub_kategori' => 'Diskusi / Tanya Jawab',
                'indikator' => [
                    'Penguasaan Materi',
                    'Sistematika Menjawab',
                    'Mempertahankan Pendapat',
                    'Sikap Penerimaan'
                ]
            ],
            // MAKALAH (Mutu Penulisan Makalah)
            [
                'jenis' => 'makalah',
                'sub_kategori' => 'Teknik Penulisan',
                'indikator' => [
                    'Sistematika Penulisan',
                    'Kelengkapan Isi',
                    'Review Kepustakaan',
                    'Bahasa Tulisan'
                ]
            ],
            [
                'jenis' => 'makalah',
                'sub_kategori' => 'Pokok Bahasan',
                'indikator' => [
                    'Perumusan Masalah',
                    'Analisa Uraian',
                    'Penyelesaian Masalah',
                    'Kesimpulan & Saran'
                ]
            ],
            // DISIPLIN & PRESTASI
            [
                'jenis' => 'disiplin_prestasi',
                'sub_kategori' => 'Penilaian Prestasi',
                'indikator' => [
                    'Kerajinan',
                    'Kesungguhan Kerja',
                    'Kecakapan',
                    'Kemandirian',
                    'Inisiatif'
                ]
            ],
            [
                'jenis' => 'disiplin_prestasi',
                'sub_kategori' => 'Penilaian Supervisi',
                'indikator' => [
                    'Kecakapan (Kemampuan)',
                    'Inisiatif',
                    'Kepemimpinan',
                    'Komunikasi',
                    'Kerjasama'
                ]
            ],
            // KUISIONER MENTOR
            [
                'jenis' => 'kuisioner_mentor',
                'sub_kategori' => 'HARDSKILL',
                'indikator' => [
                    'Keahlian pada kompetensi utama (Informatika / Pemrograman)',
                    'Keahlian pada kompetensi utama (Keamanan Jaringan)',
                    'Keahlian pada kompetensi utama (Kontrol / Mikrokontroler / PLC)',
                    'Keahlian pada kompetensi utama (Listrik dan Elektronika)',
                    'Kemampuan komunikasi global (B. Inggris)',
                    'Kemampuan penggunaan TI (Software Aplikasi)',
                    'Kemampuan merawat/memakai alat dengan benar'
                ]
            ],
            [
                'jenis' => 'kuisioner_mentor',
                'sub_kategori' => 'SOFTSKILL',
                'indikator' => [
                    'Integritas (Etika/Moral)',
                    'Kemampuan berkomunikasi',
                    'Kerja sama dalam tim',
                    'Kepemimpinan',
                    'Inisiatif',
                    'Kemauan untuk belajar',
                    'Bekerja Keras & Motivasi',
                    'Kedisiplinan & Tanggung Jawab'
                ]
            ],
        ];

        foreach ($data as $item) {
            \App\Models\ParameterPenilaian::updateOrCreate(
                ['jenis' => $item['jenis'], 'sub_kategori' => $item['sub_kategori']],
                ['indikator' => $item['indikator']]
            );
        }
    }
}
