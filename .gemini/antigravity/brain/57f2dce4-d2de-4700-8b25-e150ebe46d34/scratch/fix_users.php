<?php

require 'd:/laragon/www/men/vendor/autoload.php';
$app = require_once 'd:/laragon/www/men/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'jefry@gmail.com')->first();
if ($user) {
    $user->update([
        'type' => ['superadmin'],
        'is_active' => true,
    ]);
    echo "User jefry updated to superadmin\n";
} else {
    echo "User jefry not found\n";
}

$admin = User::where('email', 'admin@gmail.com')->first();
if ($admin) {
    $admin->update([
        'type' => ['admin'],
        'permissions' => ['view_sidebar', 'add_data', 'update_data', 'delete_data'],
        'is_active' => true,
    ]);
    echo "User admin updated\n";
}
