<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Dosen;
use App\Models\KetersediaanJadwal;
use App\Models\JadwalBimbingan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ExportBimbinganTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('mahasiswa');
    }

    /** @test */
    public function test_student_can_export_bimbingan_pdf()
    {
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');
        // create related Mahasiswa record
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '12345678',
            'nama_lengkap' => 'Test Mahasiswa',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => '2024',
        ]);

        $dosenUser = User::factory()->create();
        $dosen = Dosen::create([
            'user_id' => $dosenUser->id,
            'nidn' => '1234567890',
            'kode_dosen' => 'TMD',
            'nama_lengkap' => 'Test Dosen',
            'program_studi' => 'Teknik Informatika',
            'fakultas' => 'FIF',
            'is_active' => true,
        ]);

        $ketersediaan = KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => '2026-06-15',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '10:00:00',
            'kuota' => 5,
            'tipe' => 'online',
        ]);

        $bimbingan = JadwalBimbingan::create([
            'dosen_id' => $dosen->id,
            'mahasiswa_id' => $mahasiswa->id,
            'ketersediaan_jadwal_id' => $ketersediaan->id,
            'judul_ta' => 'Judul TA Test',
            'topik_bimbingan' => 'Topik Test',
            'tipe' => 'online',
            'lokasi' => 'Zoom',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.jadwal.exportPdf', [
            'format' => 'pdf',
            'ids' => $bimbingan->id
        ]));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_student_can_export_bimbingan_excel()
    {
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '12345678',
            'nama_lengkap' => 'Test Mahasiswa',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => '2024',
        ]);

        $dosenUser = User::factory()->create();
        $dosen = Dosen::create([
            'user_id' => $dosenUser->id,
            'nidn' => '1234567890',
            'kode_dosen' => 'TMD',
            'nama_lengkap' => 'Test Dosen',
            'program_studi' => 'Teknik Informatika',
            'fakultas' => 'FIF',
            'is_active' => true,
        ]);

        $ketersediaan = KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => '2026-06-15',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '10:00:00',
            'kuota' => 5,
            'tipe' => 'online',
        ]);

        $bimbingan = JadwalBimbingan::create([
            'dosen_id' => $dosen->id,
            'mahasiswa_id' => $mahasiswa->id,
            'ketersediaan_jadwal_id' => $ketersediaan->id,
            'judul_ta' => 'Judul TA Test',
            'topik_bimbingan' => 'Topik Test',
            'tipe' => 'online',
            'lokasi' => 'Zoom',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.jadwal.exportPdf', [
            'format' => 'excel',
            'ids' => $bimbingan->id
        ]));
        $response->assertStatus(200);
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }
}
