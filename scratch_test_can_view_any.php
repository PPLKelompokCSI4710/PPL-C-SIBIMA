<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\BimbinganRelation\BimbinganRelationResource;

$admin = User::where('email', 'admin@sibima.test')->first();
Auth::login($admin);

echo "Logged in as: " . Auth::user()->email . "\n";
echo "Has role admin: " . (Auth::user()->hasRole('admin') ? 'Yes' : 'No') . "\n";
echo "canViewAny: " . (BimbinganRelationResource::canViewAny() ? 'Yes' : 'No') . "\n";
