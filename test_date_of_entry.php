<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking for clients with date_of_entry_us ===\n";
$clientWithDate = \App\Models\Client::whereNotNull('date_of_entry_us')->first();
if ($clientWithDate) {
    echo "Found client with date_of_entry_us:\n";
    echo "Client ID: " . $clientWithDate->id . "\n";
    echo "Raw date_of_entry_us: " . ($clientWithDate->attributes['date_of_entry_us'] ?? 'NOT SET') . "\n";
    echo "Decrypted date_of_entry_us: " . ($clientWithDate->date_of_entry_us ?? 'NULL') . "\n";
    
    $array = $clientWithDate->toArray();
    echo "In toArray() - date_of_entry_us: " . ($array['date_of_entry_us'] ?? 'NOT IN ARRAY') . "\n";
} else {
    echo "No clients with date_of_entry_us found\n";
    echo "All clients have NULL date_of_entry_us\n";
}
