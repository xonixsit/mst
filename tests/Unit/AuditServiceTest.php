<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Client;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuditService $auditService;
    private User $user;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->auditService = app(AuditService::class);
        
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);
        
        $this->client = Client::factory()->create();
        
        Auth::login($this->user);
    }

    public function test_can_log_model_creation()
    {
        $auditLog = $this->auditService->logCreated($this->client, [
            'context' => 'test_context'
        ]);

        $this->assertInstanceOf(AuditLog::class, $auditLog);
        $this->assertEquals('created', $auditLog->event);
        $this->assertEquals(Client::class, $auditLog->auditable_type);
        $this->assertEquals($this->client->id, $auditLog->auditable_id);
        $this->assertEquals($this->user->id, $auditLog->user_id);
        $this->assertEquals('test_context', $auditLog->metadata['context']);
        $this->assertNotNull($auditLog->new_values);
        $this->assertNull($auditLog->old_values);
    }

    public function test_can_log_model_update()
    {
        $originalName = $this->client->first_name;
        $this->client->first_name = 'Updated Name';
        
        $auditLog = $this->auditService->logUpdated($this->client, [
            'context' => 'test_update'
        ]);

        $this->assertEquals('updated', $auditLog->event);
        $this->assertEquals($originalName, $auditLog->old_values['first_name']);
        $this->assertEquals('Updated Name', $auditLog->new_values['first_name']);
        $this->assertEquals('test_update', $auditLog->metadata['context']);
    }

    public function test_can_log_model_deletion()
    {
        $auditLog = $this->auditService->logDeleted($this->client, [
            'context' => 'test_deletion'
        ]);

        $this->assertEquals('deleted', $auditLog->event);
        $this->assertNotNull($auditLog->old_values);
        $this->assertNull($auditLog->new_values);
        $this->assertEquals('test_deletion', $auditLog->metadata['context']);
    }

    public function test_can_log_status_change()
    {
        $auditLog = $this->auditService->logStatusChange(
            $this->client,
            'active',
            'inactive',
            ['reason' => 'test_status_change']
        );

        $this->assertEquals('status_changed', $auditLog->event);
        $this->assertEquals('active', $auditLog->old_values['status']);
        $this->assertEquals('inactive', $auditLog->new_values['status']);
        $this->assertEquals('test_status_change', $auditLog->metadata['reason']);
        $this->assertEquals('active', $auditLog->metadata['status_change']['from']);
        $this->assertEquals('inactive', $auditLog->metadata['status_change']['to']);
    }

    public function test_can_log_assignment_change()
    {
        $newUser = User::factory()->create();
        
        $auditLog = $this->auditService->logAssignment(
            $this->client,
            $this->user->id,
            $newUser->id,
            ['reason' => 'test_assignment']
        );

        $this->assertEquals('assigned', $auditLog->event);
        $this->assertEquals($this->user->id, $auditLog->old_values['user_id']);
        $this->assertEquals($newUser->id, $auditLog->new_values['user_id']);
        $this->assertEquals('test_assignment', $auditLog->metadata['reason']);
    }

    public function test_can_log_bulk_operation()
    {
        $clients = Client::factory()->count(3)->create();
        $clientIds = $clients->pluck('id')->toArray();
        
        $auditLog = $this->auditService->logBulkOperation(
            Client::class,
            $clientIds,
            'status_update',
            ['new_status' => 'inactive']
        );

        $this->assertEquals('bulk_status_update', $auditLog->event);
        $this->assertEquals(Client::class, $auditLog->auditable_type);
        $this->assertEquals(0, $auditLog->auditable_id); // Bulk operations use 0
        $this->assertEquals($clientIds, $auditLog->new_values['affected_ids']);
        $this->assertEquals('status_update', $auditLog->metadata['operation']);
        $this->assertEquals(3, $auditLog->metadata['affected_count']);
    }

    public function test_can_get_logs_for_model()
    {
        // Create some audit logs
        $this->auditService->logCreated($this->client);
        $this->auditService->logUpdated($this->client);
        $this->auditService->logDeleted($this->client);

        $logs = $this->auditService->getLogsForModel($this->client, 10);

        $this->assertCount(3, $logs);
        $this->assertEquals('deleted', $logs->first()->event); // Most recent first
        $this->assertEquals('created', $logs->last()->event);
    }

    public function test_can_get_logs_for_user()
    {
        $this->auditService->logCreated($this->client);
        $this->auditService->logUpdated($this->client);

        $logs = $this->auditService->getLogsForUser($this->user->id, 10);

        $this->assertCount(2, $logs);
        $this->assertEquals($this->user->id, $logs->first()->user_id);
    }

    public function test_can_get_recent_logs_with_filters()
    {
        $this->auditService->logCreated($this->client);
        $this->auditService->logUpdated($this->client);

        // Test event filter
        $logs = $this->auditService->getRecentLogs(10, ['event' => 'created']);
        $this->assertCount(1, $logs);
        $this->assertEquals('created', $logs->first()->event);

        // Test user filter
        $logs = $this->auditService->getRecentLogs(10, ['user_id' => $this->user->id]);
        $this->assertCount(2, $logs);

        // Test model type filter
        $logs = $this->auditService->getRecentLogs(10, ['model_type' => Client::class]);
        $this->assertCount(2, $logs);
    }

    public function test_can_cleanup_old_logs()
    {
        // Create old audit logs
        $oldLog = AuditLog::factory()->create([
            'created_at' => now()->subDays(400)
        ]);
        
        $recentLog = AuditLog::factory()->create([
            'created_at' => now()->subDays(100)
        ]);

        $deletedCount = $this->auditService->cleanup(365); // Keep 365 days

        $this->assertEquals(1, $deletedCount);
        $this->assertDatabaseMissing('audit_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('audit_logs', ['id' => $recentLog->id]);
    }

    public function test_audit_log_includes_request_metadata()
    {
        $this->withHeaders([
            'User-Agent' => 'Test Browser',
            'X-Forwarded-For' => '192.168.1.1'
        ]);

        $auditLog = $this->auditService->logCreated($this->client);

        $this->assertNotNull($auditLog->ip_address);
        $this->assertNotNull($auditLog->user_agent);
    }
}