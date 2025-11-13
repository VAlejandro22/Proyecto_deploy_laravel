<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::find(19);
$token = $user->createToken('Test Payment API')->plainTextToken;

echo "Nuevo token para " . $user->name . " (ID: " . $user->id . "):" . PHP_EOL;
echo $token . PHP_EOL;
