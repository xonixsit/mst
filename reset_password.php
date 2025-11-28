<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'xonixsitsolutions@gmail.com')->first();

if ($user) {
    $user->password = Hash::make('password123');
    $user->save();
    echo "Password reset successfully for user: {$user->email}\n";
    echo "New password: password123\n";
} else {
    echo "User with email 'xonixsitsolutions@gmail.com' not found.\n";
}