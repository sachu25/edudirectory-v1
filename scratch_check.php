<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Total Colleges: " . \App\Models\College::count() . "\n";
echo "Colleges with Status 'active': " . \App\Models\College::where('status', 'active')->count() . "\n";
echo "Colleges with Status 'inactive': " . \App\Models\College::where('status', 'inactive')->count() . "\n";

echo "\nFirst 5 Colleges with Status details:\n";
foreach (\App\Models\College::take(5)->get() as $col) {
    echo "- ID: {$col->id}, Name: '{$col->name}', Status: '{$col->status}'\n";
}
