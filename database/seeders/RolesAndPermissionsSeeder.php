<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Client management permissions
            ['name' => 'View Clients', 'slug' => 'clients.view', 'category' => 'client', 'description' => 'View client information'],
            ['name' => 'Create Clients', 'slug' => 'clients.create', 'category' => 'client', 'description' => 'Create new client records'],
            ['name' => 'Update Clients', 'slug' => 'clients.update', 'category' => 'client', 'description' => 'Update client information'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'category' => 'client', 'description' => 'Delete client records'],
            ['name' => 'Export Client Data', 'slug' => 'clients.export', 'category' => 'client', 'description' => 'Export client data'],
            ['name' => 'Import Client Data', 'slug' => 'clients.import', 'category' => 'client', 'description' => 'Import client data'],
            ['name' => 'View Sensitive Data', 'slug' => 'clients.view-sensitive', 'category' => 'client', 'description' => 'View sensitive client data like SSN'],
            ['name' => 'Assign Clients', 'slug' => 'clients.assign', 'category' => 'client', 'description' => 'Assign clients to tax professionals'],
            
            // Invoice management permissions
            ['name' => 'View Invoices', 'slug' => 'invoices.view', 'category' => 'invoice', 'description' => 'View invoice information'],
            ['name' => 'Create Invoices', 'slug' => 'invoices.create', 'category' => 'invoice', 'description' => 'Create new invoices'],
            ['name' => 'Update Invoices', 'slug' => 'invoices.update', 'category' => 'invoice', 'description' => 'Update invoice information'],
            ['name' => 'Delete Invoices', 'slug' => 'invoices.delete', 'category' => 'invoice', 'description' => 'Delete invoices'],
            ['name' => 'Send Invoices', 'slug' => 'invoices.send', 'category' => 'invoice', 'description' => 'Send invoices to clients'],
            
            // Document management permissions
            ['name' => 'View Documents', 'slug' => 'documents.view', 'category' => 'document', 'description' => 'View client documents'],
            ['name' => 'Upload Documents', 'slug' => 'documents.upload', 'category' => 'document', 'description' => 'Upload documents'],
            ['name' => 'Delete Documents', 'slug' => 'documents.delete', 'category' => 'document', 'description' => 'Delete documents'],
            ['name' => 'Download Documents', 'slug' => 'documents.download', 'category' => 'document', 'description' => 'Download documents'],
            
            // Audit and compliance permissions
            ['name' => 'View Audit Logs', 'slug' => 'audit.view', 'category' => 'audit', 'description' => 'View audit logs'],
            ['name' => 'Export Audit Logs', 'slug' => 'audit.export', 'category' => 'audit', 'description' => 'Export audit logs'],
            ['name' => 'Archive Data', 'slug' => 'data.archive', 'category' => 'audit', 'description' => 'Archive client data'],
            ['name' => 'Restore Data', 'slug' => 'data.restore', 'category' => 'audit', 'description' => 'Restore archived data'],
            
            // Communication permissions
            ['name' => 'Send Messages', 'slug' => 'messages.send', 'category' => 'communication', 'description' => 'Send messages to clients'],
            ['name' => 'View Messages', 'slug' => 'messages.view', 'category' => 'communication', 'description' => 'View client messages'],
            ['name' => 'Bulk Communications', 'slug' => 'communications.bulk', 'category' => 'communication', 'description' => 'Send bulk communications'],
            
            // System administration permissions
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'category' => 'admin', 'description' => 'Manage user accounts'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'category' => 'admin', 'description' => 'Manage roles and permissions'],
            ['name' => 'System Settings', 'slug' => 'system.settings', 'category' => 'admin', 'description' => 'Manage system settings'],
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'category' => 'admin', 'description' => 'View business analytics'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'level' => 100,
                'permissions' => Permission::all()->pluck('slug')->toArray()
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to most features',
                'level' => 90,
                'permissions' => [
                    'clients.view', 'clients.create', 'clients.update', 'clients.delete',
                    'clients.export', 'clients.import', 'clients.view-sensitive', 'clients.assign',
                    'invoices.view', 'invoices.create', 'invoices.update', 'invoices.delete', 'invoices.send',
                    'documents.view', 'documents.upload', 'documents.delete', 'documents.download',
                    'audit.view', 'audit.export', 'data.archive', 'data.restore',
                    'messages.send', 'messages.view', 'communications.bulk',
                    'analytics.view'
                ]
            ],
            [
                'name' => 'Tax Professional',
                'slug' => 'tax-professional',
                'description' => 'Tax professional with client management access',
                'level' => 70,
                'permissions' => [
                    'clients.view', 'clients.create', 'clients.update',
                    'clients.export', 'clients.view-sensitive',
                    'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send',
                    'documents.view', 'documents.upload', 'documents.download',
                    'messages.send', 'messages.view',
                    'audit.view'
                ]
            ],
            [
                'name' => 'Assistant',
                'slug' => 'assistant',
                'description' => 'Limited access for administrative assistants',
                'level' => 50,
                'permissions' => [
                    'clients.view', 'clients.create', 'clients.update',
                    'invoices.view', 'invoices.create',
                    'documents.view', 'documents.upload',
                    'messages.send', 'messages.view'
                ]
            ],
            [
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'Client access to own information only',
                'level' => 10,
                'permissions' => [
                    'documents.upload', 'documents.view', 'documents.download',
                    'messages.send', 'messages.view'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            // Assign permissions to role
            $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}