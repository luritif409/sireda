<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = $argv[1] ?? null;
$password = $argv[2] ?? 'password';
$role = $argv[3] ?? 'mahasiswa';
$name = $argv[4] ?? null;

if (!$email) {
    echo "Usage: php create_or_update_user.php email [password] [role] [name]\n";
    exit(1);
}

if (!$name) {
    $name = explode('@', $email)[0];
}

$user = User::firstOrNew(['email' => $email]);
$user->name = $name;
$user->password = Hash::make($password);
$user->role = $role;
$user->save();

echo "User saved: {$user->email} (name: {$user->name}) role={$user->role}\n";
