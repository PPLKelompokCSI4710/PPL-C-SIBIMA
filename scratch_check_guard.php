<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$admin = User::where('email', 'admin@sibima.test')->first();
if ($admin) {
    foreach ($admin->roles as $role) {
        echo "Role: " . $role->name . " | Guard: " . $role->guard_name . "\n";
    }
} else {
    echo "Admin user not found.\n";
}
