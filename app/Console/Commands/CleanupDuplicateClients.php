<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class CleanupDuplicateClients extends Command
{
    protected $signature = 'cleanup:duplicate-clients {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up duplicate client records for the same user';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        // Get all clients grouped by user_id
        $clients = Client::with('user')->get();
        $clientsByUser = $clients->groupBy('user_id');
        
        $duplicates = $clientsByUser->filter(function ($group) {
            return $group->count() > 1;
        });

        if ($duplicates->count() === 0) {
            $this->info('No duplicate clients found.');
            return 0;
        }

        $this->info("Found duplicates for {$duplicates->count()} users:");

        $totalDeleted = 0;

        foreach ($duplicates as $userId => $clientGroup) {
            $user = $clientGroup->first()->user;
            $this->warn("User: {$user->first_name} {$user->last_name} ({$user->email}) has {$clientGroup->count()} client records");

            // Keep the most recent client record (or the one with most data)
            $clientsToKeep = $clientGroup->sortByDesc(function ($client) {
                // Score based on completeness and recency
                $score = 0;
                
                // More recent = higher score
                $score += $client->created_at->timestamp / 1000000;
                
                // Has more data = higher score
                if ($client->phone) $score += 10;
                if ($client->ssn) $score += 10;
                if ($client->date_of_birth) $score += 10;
                if ($client->marital_status) $score += 5;
                if ($client->occupation) $score += 5;
                
                // Active status = higher score
                if ($client->status === 'active') $score += 20;
                
                return $score;
            });

            $keepClient = $clientsToKeep->first();
            $deleteClients = $clientsToKeep->slice(1);

            $this->line("  Keeping: Client ID {$keepClient->id} (Status: {$keepClient->status}, Created: {$keepClient->created_at})");
            
            foreach ($deleteClients as $client) {
                $this->line("  Deleting: Client ID {$client->id} (Status: {$client->status}, Created: {$client->created_at})");
                
                if (!$dryRun) {
                    // Delete related records first
                    $client->spouse()?->delete();
                    $client->employees()->delete();
                    $client->projects()->delete();
                    $client->assets()->delete();
                    $client->expenses()->delete();
                    
                    // Delete the client
                    $client->delete();
                    $totalDeleted++;
                }
            }
        }

        if ($dryRun) {
            $this->info("Would delete {$duplicates->sum(fn($group) => $group->count() - 1)} duplicate client records.");
            $this->info("Run without --dry-run to actually perform the cleanup.");
        } else {
            $this->info("Deleted {$totalDeleted} duplicate client records.");
        }

        return 0;
    }
}