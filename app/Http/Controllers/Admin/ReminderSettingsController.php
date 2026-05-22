<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReminderSettingsController extends Controller
{
    public function edit()
    {
        $inactiveDays = max(1, (int) (AppSetting::get('progress_reminder_inactive_days') ?? 14));
        $escalationThreshold = max(1, (int) (AppSetting::get('escalation_reminder_threshold') ?? 3));

        return Inertia::render('Admin/ReminderSettings', [
            'settings' => [
                'progress_reminder_inactive_days' => $inactiveDays,
                'escalation_reminder_threshold' => $escalationThreshold,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'progress_reminder_inactive_days' => ['required', 'integer', 'min:1', 'max:365'],
            'escalation_reminder_threshold' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        AppSetting::set('progress_reminder_inactive_days', $data['progress_reminder_inactive_days']);
        AppSetting::set('escalation_reminder_threshold', $data['escalation_reminder_threshold']);

        return redirect()->back()->with('success', 'Konfigurasi admin berhasil diperbarui.');
    }
}
