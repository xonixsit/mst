<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Document;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DataArchivalControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    public function test_admin_can_view_data_archival_index()
    {
        // Create test data for statistics
        Client::factory()->create(['status' => 'active']);
        Client::factory()->create(['status' => 'inactive']);
        Client::factory()->create(['status' => 'archived', 'archived_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.data-archival.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/DataArchival/Index')
                ->has('stats')
                ->has('recommendations')
                ->where('stats.active_clients', 1)
                ->where('stats.inactive_clients', 1)
                ->where('stats.archived_clients', 1)
        );
    }

    public function test_admin_can_archive_inactive_clients()
    {
        // Create inactive clients
        $inactiveClient = Client::factory()->create([
            'status' => 'inactive',
            'updated_at' => Carbon::now()->subDays(400)
        ]);
        
        // Create active client (should not be archived)
        $activeClient = Client::factory()->create([
            'status' => 'active',
            'updated_at' => Carbon::now()->subDays(400)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.archive-inactive-clients'), [
                'inactive_days' => 365
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify inactive client was archived
        $inactiveClient->refresh();
        $activeClient->refresh();
        
        $this->assertEquals('archived', $inactiveClient->status);
        $this->assertNotNull($inactiveClient->archived_at);
        $this->assertEquals('active', $activeClient->status);
        $this->assertNull($activeClient->archived_at);
    }

    public function test_admin_can_restore_archived_client()
    {
        $archivedClient = Client::factory()->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(30)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.restore-client', $archivedClient));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify client was restored
        $archivedClient->refresh();
        $this->assertEquals('active', $archivedClient->status);
        $this->assertNull($archivedClient->archived_at);
    }

    public function test_cannot_restore_non_archived_client()
    {
        $activeClient = Client::factory()->create([
            'status' => 'active',
            'archived_at' => null
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.restore-client', $activeClient));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_permanently_delete_archived_data()
    {
        // Create old archived client
        $oldArchivedClient = Client::factory()->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(3000)
        ]);
        
        // Create recently archived client
        $recentArchivedClient = Client::factory()->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(100)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.permanently-delete-archived-data'), [
                'archive_days' => 2555,
                'confirm_deletion' => true
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify old client was deleted
        $this->assertDatabaseMissing('clients', ['id' => $oldArchivedClient->id]);
        
        // Verify recent client still exists
        $this->assertDatabaseHas('clients', ['id' => $recentArchivedClient->id]);
    }

    public function test_admin_can_view_archived_clients()
    {
        // Create archived clients
        $archivedClient1 = Client::factory()->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(30)
        ]);
        
        $archivedClient2 = Client::factory()->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(60)
        ]);
        
        // Create active client (should not appear)
        Client::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.data-archival.archived-clients'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/DataArchival/ArchivedClients')
                ->has('clients.data', 2)
        );
    }

    public function test_admin_can_search_archived_clients()
    {
        $archivedClient = Client::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(30)
        ]);
        
        Client::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(30)
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.data-archival.archived-clients', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('clients.data', 1)
                ->where('filters.search', 'John')
        );
    }

    public function test_admin_can_view_retention_policy()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.data-archival.retention-policy'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/DataArchival/RetentionPolicy')
                ->has('policies')
        );
    }

    public function test_admin_can_update_retention_policy()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.update-retention-policy'), [
                'inactive_client_archival_days' => 365,
                'archived_data_retention_days' => 2555,
                'audit_log_retention_days' => 2555,
                'document_retention_days' => 2555,
                'auto_archival_enabled' => false,
                'auto_deletion_enabled' => false
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_execute_archival_policy()
    {
        // Create inactive client eligible for archival
        Client::factory()->create([
            'status' => 'inactive',
            'updated_at' => Carbon::now()->subDays(400)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.execute-retention-policy'), [
                'policy_type' => 'archival'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_execute_deletion_policy()
    {
        // Create old archived client eligible for deletion
        Client::factory()->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(3000)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.execute-retention-policy'), [
                'policy_type' => 'deletion'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_archival_validates_inactive_days()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.archive-inactive-clients'), [
                'inactive_days' => 10 // Too low
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('inactive_days');
    }

    public function test_deletion_requires_confirmation()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.permanently-delete-archived-data'), [
                'archive_days' => 2555,
                'confirm_deletion' => false
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('confirm_deletion');
    }

    public function test_retention_policy_validates_minimum_days()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.data-archival.update-retention-policy'), [
                'inactive_client_archival_days' => 10, // Too low
                'archived_data_retention_days' => 100, // Too low
                'audit_log_retention_days' => 2555,
                'document_retention_days' => 2555,
                'auto_archival_enabled' => false,
                'auto_deletion_enabled' => false
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['inactive_client_archival_days', 'archived_data_retention_days']);
    }

    public function test_non_admin_cannot_access_data_archival()
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)
            ->get(route('admin.data-archival.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_data_archival()
    {
        $response = $this->get(route('admin.data-archival.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_archived_clients_pagination_works()
    {
        // Create many archived clients
        Client::factory()->count(30)->create([
            'status' => 'archived',
            'archived_at' => Carbon::now()->subDays(30)
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.data-archival.archived-clients', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('clients.data', 10)
                ->where('clients.per_page', 10)
                ->where('clients.total', 30)
        );
    }
}