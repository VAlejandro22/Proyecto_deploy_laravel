<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::find(19);
$user->assignRole('Cliente');
echo 'Rol Cliente asignado a ' . $user->name . PHP_EOL;
