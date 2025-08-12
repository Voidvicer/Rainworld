<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

echo "Testing roles:\n\n";

$users = App\Models\User::with('roles')->get();

foreach ($users as $user) {
    echo "User: {$user->email}\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Has admin role: " . ($user->hasRole('admin') ? 'Yes' : 'No') . "\n";
    echo "Has any role: " . ($user->hasAnyRole(['admin', 'hotel_manager']) ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\nAll roles in system:\n";
$roles = Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "- {$role->name}\n";
}
