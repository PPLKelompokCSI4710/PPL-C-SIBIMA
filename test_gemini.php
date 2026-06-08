<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$key = config('services.gemini.key');
if (! $key) {
    echo "No API Key\n";
    exit;
}

$response = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key={$key}");
echo $response->body();
