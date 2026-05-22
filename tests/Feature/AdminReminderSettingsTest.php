<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminReminderSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['admin', 'dosen', 'mahasiswa'] as $name) {
            Role::findOrCreate($name);
        }
    }

    public function test_admin_reminder_settings_includes_escalation_threshold_in_inertia_props(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.settings.reminders.edit'))
            ->assertOk()
            ->assertInertia(fn (AssertableJson $page) => $page
                ->component('Admin/ReminderSettings')
                ->has('settings', fn (AssertableJson $settings) => $settings
                    ->where('progress_reminder_inactive_days', 14)
                    ->where('escalation_reminder_threshold', 3)
                    ->etc()
                )
            );
    }
}
