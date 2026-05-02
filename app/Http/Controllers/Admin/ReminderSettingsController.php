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
        $inactiveDays = (int) (AppSetting::get('progress_reminder_inactive_days', 14) ?? 14);

        return Inertia::render('Admin/ReminderSettings', [
            'settings' => [
                'progress_reminder_inactive_days' => $inactiveDays,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'progress_reminder_inactive_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        AppSetting::set('progress_reminder_inactive_days', $data['progress_reminder_inactive_days']);

        return redirect()->back()->with('success', 'Konfigurasi admin berhasil diperbarui.');
    }
}
