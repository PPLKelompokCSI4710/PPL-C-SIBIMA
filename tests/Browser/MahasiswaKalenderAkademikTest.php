<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MahasiswaKalenderAkademikTest extends DuskTestCase
{
    /**
     * Test 1: Mahasiswa dapat melakukan request jadwal bimbingan.
     */
    public function test_mahasiswa_can_request_jadwal(): void
    {
        // Reset state
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();
            $dosen = User::where('email', 'siti@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-mahasiswa')
                ->waitFor('#btn-request-jadwal', 15)
                ->click('#btn-request-jadwal')
                ->waitForText('Request Jadwal Bimbingan', 5)
                ->type('#mhs-judul', 'Request Bimbingan Dusk')
                ->select('#mhs-dosen-id', (string) $dosen->id)
                ->script([
                    "document.getElementById('mhs-tanggal').value = '2026-05-28'",
                    "document.getElementById('mhs-tanggal').dispatchEvent(new Event('input'))",
                    "document.getElementById('mhs-jam').value = '10:00'",
                    "document.getElementById('mhs-jam').dispatchEvent(new Event('input'))",
                ]);

            $browser->click('#btn-submit-request')
                ->waitForText('Request Bimbingan Dusk', 20)
                ->assertSee('Request Bimbingan Dusk')
                ->assertSee('PENDING DOSEN');
        });
    }

    /**
     * Test 2: Mahasiswa dapat melihat link Google Calendar.
     */
    public function test_mahasiswa_can_see_google_calendar_link(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-mahasiswa')
                ->waitForText('Bimbingan Skripsi Bab 3 – Mhs1 & Siti', 15)
                ->script("
                        const elements = document.querySelectorAll('div.cursor-pointer h4');
                        for (let el of elements) {
                            if (el.innerText.includes('Bimbingan Skripsi Bab 3')) {
                                el.closest('.cursor-pointer').click();
                                break;
                            }
                        }
                    ");

            $browser->waitFor('a[title="Simpan ke Google Calendar"]', 10)
                ->assertPresent('a[title="Simpan ke Google Calendar"]')
                ->assertAttributeContains('a[title="Simpan ke Google Calendar"]', 'href', 'calendar.google.com/calendar/render');
        });
    }
}
