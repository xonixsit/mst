<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins and tax professionals can view all clients
        return $user->isTaxProfessional();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        // Admins and tax professionals can view any client
        if ($user->isTaxProfessional()) {
            return true;
        }
        
        // Clients can only view their own information
        if ($user->isClient()) {
            return $user->email === $client->email;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins and tax professionals can create client records
        // Clients can create their own record during registration
        return $user->isTaxProfessional() || $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        // Admins and tax professionals can update any client
        if ($user->isTaxProfessional()) {
            return true;
        }
        
        // Clients can only update their own information
        if ($user->isClient()) {
            return $user->email === $client->email;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        // Only admins can delete client records
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        // Only admins can restore client records
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        // Only admins can permanently delete client records
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can export client data.
     */
    public function export(User $user, Client $client): bool
    {
        // Admins and tax professionals can export any client data
        if ($user->isTaxProfessional()) {
            return true;
        }
        
        // Clients can export their own data
        if ($user->isClient()) {
            return $user->email === $client->email;
        }
        
        return false;
    }

    /**
     * Determine whether the user can request review for client information.
     */
    public function requestReview(User $user, Client $client): bool
    {
        // Only clients can request review of their own information
        if ($user->isClient()) {
            return $user->email === $client->email;
        }
        
        return false;
    }

    /**
     * Determine whether the user can assign the client to a tax professional.
     */
    public function assign(User $user, Client $client): bool
    {
        // Only admins can assign clients to tax professionals
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view audit logs for the client.
     */
    public function viewAuditLogs(User $user, Client $client): bool
    {
        // Only admins and tax professionals can view audit logs
        return $user->isTaxProfessional();
    }
}
