<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\User;
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
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '12345678',
            'nama_lengkap' => 'Test Mahasiswa',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => '2024',
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.jadwal.exportPdf', ['format' => 'pdf']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_student_can_export_bimbingan_excel()
    {
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '12345678',
            'nama_lengkap' => 'Test Mahasiswa',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => '2024',
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.jadwal.exportPdf', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }
}
