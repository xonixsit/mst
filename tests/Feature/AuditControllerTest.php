<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuditControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
        
        $this->client = Client::factory()->create();
    }

    public function test_admin_can_view_audit_logs_index()
    {
        // Create some audit logs
        AuditLog::factory()->count(3)->create([
            'auditable_type' => Client::class,
            'auditable_id' => $this->client->id,
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/Audit/Index')
                ->has('auditLogs.data', 3)
                ->has('users')
                ->has('modelTypes')
                ->has('eventTypes')
        );
    }

    public function test_admin_can_filter_audit_logs()
    {
        // Create audit logs with different events
        AuditLog::factory()->create([
            'event' => 'created',
            'user_id' => $this->admin->id
        ]);
        
        AuditLog::factory()->create([
            'event' => 'updated',
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.index', ['event' => 'created']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('auditLogs.data', 1)
                ->where('filters.event', 'created')
        );
    }

    public function test_admin_can_search_audit_logs()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        
        AuditLog::factory()->create([
            'user_id' => $user->id
        ]);
        
        AuditLog::factory()->create([
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('auditLogs.data', 1)
                ->where('filters.search', 'John')
        );
    }

    public function test_admin_can_view_audit_log_details()
    {
        $auditLog = AuditLog::factory()->create([
            'auditable_type' => Client::class,
            'auditable_id' => $this->client->id,
            'user_id' => $this->admin->id,
            'event' => 'updated',
            'old_values' => ['name' => 'Old Name'],
            'new_values' => ['name' => 'New Name']
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.show', $auditLog));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/Audit/Show')
                ->where('auditLog.id', $auditLog->id)
                ->where('auditLog.event', 'updated')
        );
    }

    public function test_admin_can_export_audit_logs()
    {
        AuditLog::factory()->count(5)->create([
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('audit_logs_', $response->headers->get('content-disposition'));
    }

    public function test_admin_can_generate_compliance_report()
    {
        AuditLog::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'created_at' => now()->subDays(15)
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.compliance-report', [
                'start_date' => now()->subDays(30)->toDateString(),
                'end_date' => now()->toDateString()
            ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/Audit/ComplianceReport')
                ->has('statistics')
                ->where('statistics.total_events', 10)
        );
    }

    public function test_admin_can_cleanup_old_audit_logs()
    {
        // Create old audit logs
        AuditLog::factory()->count(3)->create([
            'created_at' => now()->subDays(400)
        ]);
        
        // Create recent audit logs
        AuditLog::factory()->count(2)->create([
            'created_at' => now()->subDays(100)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.audit.cleanup'), [
                'days_to_keep' => 365
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify old logs were deleted
        $this->assertEquals(2, AuditLog::count());
    }

    public function test_non_admin_cannot_access_audit_logs()
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)
            ->get(route('admin.audit.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_audit_logs()
    {
        $response = $this->get(route('admin.audit.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_audit_log_pagination_works()
    {
        AuditLog::factory()->count(30)->create([
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('auditLogs.data', 10)
                ->where('auditLogs.per_page', 10)
                ->where('auditLogs.total', 30)
        );
    }

    public function test_compliance_report_validates_date_range()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.compliance-report', [
                'start_date' => now()->toDateString(),
                'end_date' => now()->subDays(1)->toDateString() // End before start
            ]));

        $response->assertStatus(302); // Validation error redirect
        $response->assertSessionHasErrors('end_date');
    }

    public function test_audit_export_respects_filters()
    {
        // Create audit logs with different events
        AuditLog::factory()->create([
            'event' => 'created',
            'user_id' => $this->admin->id
        ]);
        
        AuditLog::factory()->create([
            'event' => 'updated',
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.export', ['event' => 'created']));

        $response->assertStatus(200);
        
        // Check that the CSV content contains only created events
        $content = $response->getContent();
        $this->assertStringContainsString('created', $content);
        $this->assertStringNotContainsString('updated', $content);
    }
}