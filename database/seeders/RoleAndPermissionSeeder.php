<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all permissions grouped by module
        $permissions = [
            // Universities
            ['name' => 'universities.view', 'label' => 'View Universities', 'module' => 'Universities'],
            ['name' => 'universities.create', 'label' => 'Add Universities', 'module' => 'Universities'],
            ['name' => 'universities.edit', 'label' => 'Edit Universities', 'module' => 'Universities'],
            ['name' => 'universities.delete', 'label' => 'Delete Universities', 'module' => 'Universities'],
            
            // Colleges
            ['name' => 'colleges.view', 'label' => 'View Colleges', 'module' => 'Colleges'],
            ['name' => 'colleges.create', 'label' => 'Add Colleges', 'module' => 'Colleges'],
            ['name' => 'colleges.edit', 'label' => 'Edit Colleges', 'module' => 'Colleges'],
            ['name' => 'colleges.delete', 'label' => 'Delete Colleges', 'module' => 'Colleges'],
            
            // Contacts
            ['name' => 'contacts.view', 'label' => 'View Contacts', 'module' => 'Contacts'],
            ['name' => 'contacts.create', 'label' => 'Add Contacts', 'module' => 'Contacts'],
            ['name' => 'contacts.edit', 'label' => 'Edit Contacts', 'module' => 'Contacts'],
            ['name' => 'contacts.delete', 'label' => 'Delete Contacts', 'module' => 'Contacts'],

            // Interactions
            ['name' => 'interactions.view', 'label' => 'View Interactions', 'module' => 'Interactions'],
            ['name' => 'interactions.create', 'label' => 'Add Interactions', 'module' => 'Interactions'],
            ['name' => 'interactions.edit', 'label' => 'Edit Interactions', 'module' => 'Interactions'],
            ['name' => 'interactions.delete', 'label' => 'Delete Interactions', 'module' => 'Interactions'],

            // Users
            ['name' => 'users.view', 'label' => 'View Users', 'module' => 'Users'],
            ['name' => 'users.create', 'label' => 'Add Users', 'module' => 'Users'],
            ['name' => 'users.edit', 'label' => 'Edit Users', 'module' => 'Users'],
            ['name' => 'users.delete', 'label' => 'Delete Users', 'module' => 'Users'],

            // Roles & Permissions
            ['name' => 'roles.view', 'label' => 'View Roles', 'module' => 'Roles & Permissions'],
            ['name' => 'roles.manage', 'label' => 'Manage Roles & Permissions', 'module' => 'Roles & Permissions'],

            // Non-Academic Clients
            ['name' => 'non_academic_clients.view', 'label' => 'View Corporate Clients', 'module' => 'Non-Academic Clients'],
            ['name' => 'non_academic_clients.create', 'label' => 'Add Corporate Clients', 'module' => 'Non-Academic Clients'],
            ['name' => 'non_academic_clients.edit', 'label' => 'Edit Corporate Clients', 'module' => 'Non-Academic Clients'],
            ['name' => 'non_academic_clients.delete', 'label' => 'Delete Corporate Clients', 'module' => 'Non-Academic Clients'],

            // Non-Academic Interactions
            ['name' => 'non_academic_interactions.view', 'label' => 'View Corporate Interactions', 'module' => 'Non-Academic Interactions'],
            ['name' => 'non_academic_interactions.create', 'label' => 'Add Corporate Interactions', 'module' => 'Non-Academic Interactions'],
            ['name' => 'non_academic_interactions.edit', 'label' => 'Edit Corporate Interactions', 'module' => 'Non-Academic Interactions'],
            ['name' => 'non_academic_interactions.delete', 'label' => 'Delete Corporate Interactions', 'module' => 'Non-Academic Interactions'],

            // Reports
            ['name' => 'reports.view', 'label' => 'View Reports Center', 'module' => 'Reports'],
            ['name' => 'reports.staff.view', 'label' => 'View Staff Performance Reports', 'module' => 'Reports'],
            ['name' => 'reports.export', 'label' => 'Export Reports (Excel/CSV/PDF)', 'module' => 'Reports'],

            // Bulk Data Imports
            ['name' => 'imports.view', 'label' => 'View Data Imports', 'module' => 'Bulk Imports'],
            ['name' => 'imports.execute', 'label' => 'Execute Bulk Data Imports', 'module' => 'Bulk Imports'],

            // System Masters
            ['name' => 'designations.manage', 'label' => 'Manage Designations', 'module' => 'System Masters'],
            ['name' => 'purposes.manage', 'label' => 'Manage Interaction Purposes', 'module' => 'System Masters'],
            ['name' => 'interaction_statuses.manage', 'label' => 'Manage Interaction Statuses', 'module' => 'System Masters'],
            ['name' => 'contact_modes.manage', 'label' => 'Manage Contact Modes', 'module' => 'System Masters'],
        ];

        // Insert permissions
        $permissionIds = [];
        foreach ($permissions as $p) {
            $permission = Permission::updateOrCreate(
                ['name' => $p['name']],
                [
                    'label' => $p['label'],
                    'module' => $p['module']
                ]
            );
            $permissionIds[$p['name']] = $permission->id;
        }

        // Fetch default roles
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $userRole = Role::where('name', 'user')->first();

        // 1. Assign all permissions to Admin Role
        if ($adminRole) {
            $adminRole->permissions()->sync(array_values($permissionIds));
        }

        // 2. Assign viewing and editing permissions to Staff Role
        if ($staffRole) {
            $staffPermissions = [
                'universities.view', 'universities.create', 'universities.edit',
                'colleges.view', 'colleges.create', 'colleges.edit',
                'contacts.view', 'contacts.create', 'contacts.edit',
                'interactions.view', 'interactions.create', 'interactions.edit',
                'non_academic_clients.view', 'non_academic_clients.create', 'non_academic_clients.edit',
                'non_academic_interactions.view', 'non_academic_interactions.create', 'non_academic_interactions.edit',
                'reports.view', 'reports.export',
                'imports.view',
            ];
            
            $staffIds = [];
            foreach ($staffPermissions as $name) {
                if (isset($permissionIds[$name])) {
                    $staffIds[] = $permissionIds[$name];
                }
            }
            $staffRole->permissions()->sync($staffIds);
        }

        // 3. Assign view-only permissions to User Role
        if ($userRole) {
            $userPermissions = [
                'universities.view',
                'colleges.view',
                'contacts.view',
                'interactions.view',
                'non_academic_clients.view',
                'non_academic_interactions.view',
                'reports.view',
            ];
            
            $userIds = [];
            foreach ($userPermissions as $name) {
                if (isset($permissionIds[$name])) {
                    $userIds[] = $permissionIds[$name];
                }
            }
            $userRole->permissions()->sync($userIds);
        }
    }
}
