<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\College;
use App\Models\Designation;
use App\Models\ContactPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_contacts_index_supports_designation_filtering_in_ajax()
    {
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $adminRole->id
        ]);

        $college = College::create([
            'name' => 'Test Engineering College',
            'state' => 'Karnataka',
            'status' => 'active'
        ]);

        $designation1 = Designation::create([
            'name' => 'Professor',
            'status' => 'active'
        ]);

        $designation2 = Designation::create([
            'name' => 'HOD',
            'status' => 'active'
        ]);

        ContactPerson::create([
            'college_id' => $college->id,
            'name' => 'Alice',
            'designation_id' => $designation1->id,
            'status' => 'active'
        ]);

        ContactPerson::create([
            'college_id' => $college->id,
            'name' => 'Bob',
            'designation_id' => $designation2->id,
            'status' => 'active'
        ]);

        // Request without filter should return both
        $response = $this->actingAs($user)
            ->getJson(route('contacts.index'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $data = $response->json()['data'];
        $this->assertCount(2, $data);

        // Request with designation filter for designation1 (Professor)
        $response = $this->actingAs($user)
            ->getJson(route('contacts.index') . '?designation_id=' . $designation1->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $data = $response->json()['data'];
        $this->assertCount(1, $data);
        $this->assertEquals('Alice', $data[0]['name']);
    }

    public function test_can_export_contacts_to_excel_with_filters_and_sorting()
    {
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $adminRole->id
        ]);

        $college = College::create([
            'name' => 'XYZ College',
            'state' => 'Tamil Nadu',
            'status' => 'active'
        ]);

        $designation = Designation::create([
            'name' => 'Director',
            'status' => 'active'
        ]);

        ContactPerson::create([
            'college_id' => $college->id,
            'name' => 'Charlie',
            'designation_id' => $designation->id,
            'status' => 'active'
        ]);

        // Call the export route
        $response = $this->actingAs($user)
            ->get(route('contacts.export') . '?designation_id=' . $designation->id);

        $response->assertStatus(200);
        
        // Assert download headers
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('content-disposition', 'attachment; filename=contacts_export.xlsx');
    }
}
