<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = $argv[1] ?? 'test@example.com';
$role = $argv[2] ?? 'mahasiswa';

$updated = User::where('email', $email)->update(['role' => $role]);
if ($updated) {
    echo "Updated $email -> role=$role\n";
} else {
    echo "No user updated for $email.\n";
}
