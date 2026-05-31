<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

echo "jadwal_bimbingans columns:\n";
print_r(Schema::getColumnListing('jadwal_bimbingans'));

echo "\nBimbingan columns:\n";
print_r(Schema::getColumnListing('bimbingans'));
