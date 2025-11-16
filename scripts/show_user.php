<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = $argv[1] ?? 'test@example.com';
$user = User::where('email', $email)->first();
if (!$user) {
    echo "User not found: $email\n";
    exit(1);
}

echo "User: {$user->email}\n";
echo "Name: {$user->name}\n";
echo "Role: " . ($user->role ?? 'NULL') . "\n";
