<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarAuthController extends Controller
{
    protected function getGoogleClient()
    {
        $client = new Client;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(Calendar::CALENDAR_EVENTS);
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');

        return $client;
    }

    public function redirectToGoogle()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::error('Google Auth Callback Error: '.$request->error);

            return redirect()->route('dashboard')->with('error', 'Koneksi ke Google Calendar dibatalkan.');
        }

        if (! $request->has('code')) {
            return redirect()->route('dashboard')->with('error', 'Kode otorisasi tidak ditemukan.');
        }

        try {
            $client = $this->getGoogleClient();
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($token['error'])) {
                Log::error('Google Token Exchange Error: '.json_encode($token));

                return redirect()->route('dashboard')->with('error', 'Gagal menukarkan kode otorisasi.');
            }

            $user = Auth::user();
            if ($user) {
                $user->google_access_token = json_encode($token);
                if (isset($token['refresh_token'])) {
                    $user->google_refresh_token = $token['refresh_token'];
                }
                $user->save();
            }

            $redirectRoute = 'dashboard';
            if ($user->hasRole('mahasiswa')) {
                $redirectRoute = 'mahasiswa.calendar';
            } elseif ($user->hasRole('dosen') || $user->hasRole('admin') || $user->hasRole('staff')) {
                $redirectRoute = 'staff.calendar';
            }

            return redirect()->route($redirectRoute)->with('success', 'Google Calendar berhasil terhubung!');
        } catch (\Exception $e) {
            Log::error('Google Calendar Auth Exception: '.$e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan sistem saat menghubungkan Google Calendar.');
        }
    }
}
