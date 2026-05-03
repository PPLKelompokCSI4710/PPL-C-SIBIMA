<?php

namespace Database\Seeders;

use App\Models\JadwalRequest;
use App\Models\KalenderAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder ini mengisi data kalender yang berbeda-beda per user untuk demonstrasi:
 * - Jadwal nasional (user_id = null) → tampil di SEMUA user
 * - Jadwal Siti (user_id = siti) → tampil di Siti + mahasiswa bimbingan Siti
 * - Jadwal Rahmat (user_id = rahmat) → tampil di Rahmat + mahasiswa bimbingan Rahmat
 */
class KalenderTestSeeder extends Seeder
{
    public function run(): void
    {
        $siti = User::where('email', 'siti@sibima.test')->first();
        $rahmat = User::where('email', 'rahmat@sibima.test')->first();
        $mhs1 = User::where('email', 'mahasiswa@sibima.test')->first();
        $mhs2 = User::where('email', 'mahasiswa2@sibima.test')->first();

        // ── 1. Jadwal Nasional/Global (user_id = null, tampil ke semua) ───────────
        $global = [
            [
                'nama_kegiatan' => 'Ujian Akhir Semester Genap 2025/2026',
                'tipe_kegiatan' => 'semester',
                'tanggal_mulai' => '2026-06-09',
                'tanggal_selesai' => '2026-06-20',
                'jam_mulai' => '08:00',
                'deskripsi' => 'Jadwal UAS seluruh mahasiswa',
                'status' => 'Active',
            ],
            [
                'nama_kegiatan' => 'Libur Hari Raya Idul Adha',
                'tipe_kegiatan' => 'semester',
                'tanggal_mulai' => '2026-06-06',
                'tanggal_selesai' => '2026-06-07',
                'jam_mulai' => null,
                'deskripsi' => 'Libur nasional',
                'status' => 'Active',
            ],
            [
                'nama_kegiatan' => 'Yudisium Semester Genap',
                'tipe_kegiatan' => 'semester',
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2026-07-15',
                'jam_mulai' => '09:00',
                'deskripsi' => 'Gedung Aula Utama',
                'status' => 'Active',
            ],
        ];

        foreach ($global as $item) {
            KalenderAkademik::firstOrCreate(
                ['nama_kegiatan' => $item['nama_kegiatan'], 'user_id' => null],
                array_merge($item, ['user_id' => null])
            );
        }

        // ── 2. Jadwal pribadi Siti ─────────────────────────────────────────────────
        if ($siti) {
            $sitiJadwal = [
                [
                    'nama_kegiatan' => 'Kuliah Rekayasa Perangkat Lunak – Siti',
                    'tipe_kegiatan' => 'kuliah',
                    'tanggal_mulai' => '2026-05-06',
                    'tanggal_selesai' => '2026-05-06',
                    'jam_mulai' => '09:00',
                    'deskripsi' => 'Ruang 301 Gedung C',
                    'status' => 'Active',
                ],
                [
                    'nama_kegiatan' => 'Rapat Dosen – Jurusan Informatika (Siti)',
                    'tipe_kegiatan' => 'rapat',
                    'tanggal_mulai' => '2026-05-12',
                    'tanggal_selesai' => '2026-05-12',
                    'jam_mulai' => '13:00',
                    'deskripsi' => 'Ruang Rapat Dekanat',
                    'status' => 'Active',
                ],
                [
                    'nama_kegiatan' => 'Bimbingan TA – Kelompok Siti',
                    'tipe_kegiatan' => 'bimbingan',
                    'tanggal_mulai' => '2026-05-20',
                    'tanggal_selesai' => '2026-05-20',
                    'jam_mulai' => '10:00',
                    'deskripsi' => 'Ruang Dosen Lt. 2',
                    'status' => 'Active',
                ],
            ];

            foreach ($sitiJadwal as $item) {
                KalenderAkademik::firstOrCreate(
                    ['nama_kegiatan' => $item['nama_kegiatan'], 'user_id' => $siti->id],
                    array_merge($item, ['user_id' => $siti->id])
                );
            }
        }

        // ── 3. Jadwal pribadi Rahmat ───────────────────────────────────────────────
        if ($rahmat) {
            $rahmatJadwal = [
                [
                    'nama_kegiatan' => 'Kuliah Sistem Basis Data – Rahmat',
                    'tipe_kegiatan' => 'kuliah',
                    'tanggal_mulai' => '2026-05-07',
                    'tanggal_selesai' => '2026-05-07',
                    'jam_mulai' => '10:30',
                    'deskripsi' => 'Lab Komputer A',
                    'status' => 'Active',
                ],
                [
                    'nama_kegiatan' => 'Seminar Proposal – Bimbingan Rahmat',
                    'tipe_kegiatan' => 'bimbingan',
                    'tanggal_mulai' => '2026-05-14',
                    'tanggal_selesai' => '2026-05-14',
                    'jam_mulai' => '13:30',
                    'deskripsi' => 'Ruang Sidang Utama',
                    'status' => 'Active',
                ],
                [
                    'nama_kegiatan' => 'Koordinasi Penelitian – Tim Rahmat',
                    'tipe_kegiatan' => 'rapat',
                    'tanggal_mulai' => '2026-05-21',
                    'tanggal_selesai' => '2026-05-21',
                    'jam_mulai' => '14:00',
                    'deskripsi' => 'Zoom Meeting',
                    'status' => 'Active',
                ],
            ];

            foreach ($rahmatJadwal as $item) {
                KalenderAkademik::firstOrCreate(
                    ['nama_kegiatan' => $item['nama_kegiatan'], 'user_id' => $rahmat->id],
                    array_merge($item, ['user_id' => $rahmat->id])
                );
            }
        }

        // ── 4. JadwalRequest: Mahasiswa 1 (bimbingan dengan Siti) ─────────────────
        if ($mhs1 && $siti) {
            // Request yang sudah disetujui
            $reqBab3 = JadwalRequest::firstOrCreate(
                ['user_id' => $mhs1->id, 'judul' => 'Bimbingan Skripsi Bab 3 – Mhs1 & Siti'],
                [
                    'dosen_id' => $siti->id,
                    'tipe_request' => 'bimbingan',
                    'judul' => 'Bimbingan Skripsi Bab 3 – Mhs1 & Siti',
                    'deskripsi' => 'Revisi metodologi penelitian',
                    'tanggal' => '2026-05-20',
                    'jam' => '10:00',
                    'status' => 'approved_admin',
                ]
            );

            // Masukkan ke KalenderAkademik juga karena seeder tidak trigger controller
            KalenderAkademik::updateOrCreate(
                ['nama_kegiatan' => 'Bimbingan Skripsi Bab 3 – Mhs1 & Siti'],
                [
                    'user_id' => $siti->id,
                    'nama_kegiatan' => 'Bimbingan Skripsi Bab 3 – Mhs1 & Siti',
                    'tipe_kegiatan' => 'bimbingan',
                    'tanggal_mulai' => '2026-05-20',
                    'tanggal_selesai' => '2026-05-20',
                    'jam_mulai' => '10:00',
                    'deskripsi' => 'Revisi metodologi penelitian',
                    'status' => 'Active',
                ]
            );

            // Request yang MASIH PENDING (untuk testing ACC/Reject)
            JadwalRequest::updateOrCreate(
                ['user_id' => $mhs1->id, 'judul' => 'Permintaan Baru: Konsultasi Bab 4'],
                [
                    'dosen_id' => $siti->id,
                    'tipe_request' => 'bimbingan',
                    'judul' => 'Permintaan Baru: Konsultasi Bab 4',
                    'deskripsi' => 'Diskusi hasil pengujian',
                    'tanggal' => '2026-05-25',
                    'jam' => '09:00',
                    'status' => 'pending_dosen',
                ]
            );
            // Request yang SUDAH ACC DOSEN (untuk testing Admin Publish)
            JadwalRequest::updateOrCreate(
                ['user_id' => $mhs1->id, 'judul' => 'Bimbingan ACC Dosen - Tunggu Admin'],
                [
                    'dosen_id' => $siti->id,
                    'tipe_request' => 'bimbingan',
                    'judul' => 'Bimbingan ACC Dosen - Tunggu Admin',
                    'deskripsi' => 'Dosen sudah setuju, tinggal admin terbitkan',
                    'tanggal' => '2026-05-30',
                    'jam' => '14:00',
                    'status' => 'approved_dosen',
                ]
            );
        }

        // ── 5. JadwalRequest: Mahasiswa 2 (bimbingan dengan Rahmat) ───────────────
        if ($mhs2 && $rahmat) {
            JadwalRequest::firstOrCreate(
                ['user_id' => $mhs2->id, 'judul' => 'Bimbingan Proposal – Mhs2 & Rahmat'],
                [
                    'dosen_id' => $rahmat->id,
                    'tipe_request' => 'bimbingan',
                    'judul' => 'Bimbingan Proposal – Mhs2 & Rahmat',
                    'deskripsi' => 'Diskusi kerangka teori',
                    'tanggal' => '2026-05-14',
                    'jam' => '13:30',
                    'status' => 'approved_admin',
                ]
            );
        }
    }
}
