<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$controller = app(App\Http\Controllers\Mahasiswa\ExportBimbinganController::class);
$request = Illuminate\Http\Request::create('/mahasiswa/jadwal-bimbingan/export-pdf', 'GET');
$response = $controller->exportPdf($request);
file_put_contents(__DIR__.'/test_export.pdf', $response->getContent());
echo "PDF saved to test_export.pdf\n";
?>
