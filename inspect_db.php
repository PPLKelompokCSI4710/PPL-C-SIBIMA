<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "users columns:\n";
print_r(Schema::getColumnListing('users'));

echo "\ndosen columns:\n";
print_r(Schema::getColumnListing('dosen'));
