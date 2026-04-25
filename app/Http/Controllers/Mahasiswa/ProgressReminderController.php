<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class ProgressReminderController extends Controller
{
    public function index()
    {
        // For development, we return mock data if the user is not linked to Mahasiswa yet.
        // In a real app, we'd do auth()->user()->mahasiswa;
        
        $mahasiswa = Mahasiswa::where('user_id', auth()->id() ?? 1)->with('bimbingans')->first();
        
        $frequencyDays = $mahasiswa ? $mahasiswa->progress_reminder_frequency_days : 14;
        $isEnabled = $mahasiswa ? $mahasiswa->progress_reminder_enabled : true;
        
        $daysSinceLast = 0;
        if ($mahasiswa && $mahasiswa->bimbingans->count() > 0) {
            $lastBimbingan = $mahasiswa->bimbingans->where('status', 'selesai')->sortByDesc('waktu_selesai')->first();
            if ($lastBimbingan) {
                $daysSinceLast = Carbon::now()->diffInDays($lastBimbingan->waktu_selesai);
            }
        } else {
             $daysSinceLast = 18; // Mock value for display if no DB matches
        }

        return Inertia::render('Mahasiswa/Bimbingan/ProgressReminder', [
            'progressData' => [
                'daysSinceLastBimbingan' => $daysSinceLast,
                'status' => $daysSinceLast >= $frequencyDays ? 'Warning' : 'Good',
                'lastBimbinganDate' => Carbon::now()->subDays($daysSinceLast)->translatedFormat('d F Y'),
            ],
            'reminderSettings' => [
                'frequency_days' => $frequencyDays,
                'enabled' => (bool)$isEnabled,
            ]
        ]);
    }

    public function updateFrequency(Request $request)
    {
        $request->validate([
            'frequency_days' => 'required|integer|min:1',
            'enabled' => 'required|boolean',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', auth()->id() ?? 1)->first();
        if ($mahasiswa) {
            $mahasiswa->update([
                'progress_reminder_frequency_days' => $request->frequency_days,
                'progress_reminder_enabled' => $request->enabled,
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan reminder berhasil diperbarui.');
    }
}
