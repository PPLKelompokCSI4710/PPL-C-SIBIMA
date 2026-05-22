<?php

namespace Tests\Browser\Concerns;

use Spatie\Permission\Models\Role;

trait SeedsSpatieRoles
{
    protected function seedRoles(): void
    {
        foreach (['admin', 'dosen', 'mahasiswa'] as $name) {
            Role::findOrCreate($name);
        }
    }
}
