<?php

namespace App\Http\Controllers;

use App\Models\ReminderPreference;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReminderSettingsController extends Controller
{
    public function edit()
    {
        $pref = ReminderPreference::forUser(auth()->id());

        return Inertia::render('Reminder/Settings', [
            'preferences' => [
                'schedule_reminder_enabled' => (bool) $pref->schedule_reminder_enabled,
                'stage_h3_enabled' => (bool) $pref->stage_h3_enabled,
                'stage_h1_enabled' => (bool) $pref->stage_h1_enabled,
                'stage_h2_enabled' => (bool) $pref->stage_h2_enabled,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'schedule_reminder_enabled' => ['required', 'boolean'],
            'stage_h3_enabled' => ['required', 'boolean'],
            'stage_h1_enabled' => ['required', 'boolean'],
            'stage_h2_enabled' => ['required', 'boolean'],
        ]);

        $pref = ReminderPreference::forUser(auth()->id());
        $pref->update($data);

        return redirect()->back()->with('success', 'Pengaturan reminder berhasil diperbarui.');
    }
}
