<?php

namespace App\Services;

use App\Models\User;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client;
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account display');
    }

    /**
     * Sinkronisasi event ke Google Calendar user tertentu
     */
    public function syncEvent(User $user, $kalenderItem)
    {
        if (! $user->google_access_token) {
            return null;
        }

        $this->client->setAccessToken($user->google_access_token);

        // Refresh token jika sudah expired
        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $user->update(['google_access_token' => json_encode($newToken)]);
            } else {
                return null;
            }
        }

        $service = new Calendar($this->client);

        // Buat objek event Google
        $event = new Event([
            'summary' => $kalenderItem->nama_kegiatan,
            'location' => 'SIBIMA Universitas',
            'description' => $kalenderItem->deskripsi ?? 'Kegiatan Akademik SIBIMA',
            'start' => [
                'dateTime' => $this->formatDateTime($kalenderItem->tanggal_mulai, $kalenderItem->jam_mulai),
                'timeZone' => 'Asia/Jakarta',
            ],
            'end' => [
                'dateTime' => $this->formatDateTime($kalenderItem->tanggal_selesai ?? $kalenderItem->tanggal_mulai, $kalenderItem->jam_mulai, 1),
                'timeZone' => 'Asia/Jakarta',
            ],
        ]);

        $calendarId = $user->google_calendar_id ?? 'primary';

        try {
            return $service->events->insert($calendarId, $event);
        } catch (\Exception $e) {
            \Log::error('Google Calendar Sync Error: '.$e->getMessage());

            return null;
        }
    }

    protected function formatDateTime($date, $time, $addHours = 0)
    {
        $time = $time ?: '08:00:00';
        $dt = new \DateTime($date.' '.$time);
        if ($addHours > 0) {
            $dt->add(new \DateInterval("PT{$addHours}H"));
        }

        return $dt->format(\DateTime::RFC3339);
    }
}
