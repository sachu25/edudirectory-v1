<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "INTERACTIONS:\n";
print_r(App\Models\Interaction::with(['status', 'contactMode'])->orderBy('id', 'desc')->take(5)->get()->toArray());

echo "\nSTATUSES:\n";
print_r(App\Models\InteractionStatus::all()->toArray());

echo "\nMODES:\n";
print_r(App\Models\ContactMode::all()->toArray());
