<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Find a mahasiswa user (first one with role 'mahasiswa')
$mahasiswa = User::whereHas('roles', function($q){ $q->where('name', 'mahasiswa'); })->first();
if(!$mahasiswa){
    echo "No mahasiswa user found.\n";
    exit(1);
}
Auth::login($mahasiswa);

$controller = app(App\Http\Controllers\Mahasiswa\ExportBimbinganController::class);
$request = Illuminate\Http\Request::create('/mahasiswa/jadwal-bimbingan/export-pdf', 'GET');
$response = $controller->exportPdf($request);
file_put_contents(__DIR__.'/test_export.pdf', $response->getContent());
echo "PDF saved to test_export.pdf\n";
?>
