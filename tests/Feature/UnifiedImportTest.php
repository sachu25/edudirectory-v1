<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\College;
use App\Models\ContactPerson;
use App\Models\Designation;
use App\Imports\UnifiedImport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnifiedImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_unified_import_handles_colleges_and_contacts()
    {
        // 1. Setup DB masters
        $designation = Designation::create([
            'name' => 'Principal',
            'status' => 'active'
        ]);

        $uni = \App\Models\University::create([
            'name' => 'State Technical University',
            'status' => 'active',
        ]);

        // Seed an existing college
        $college = College::create([
            'name' => 'ACE College of Engineering',
            'is_university' => false,
            'type' => 'Affiliated',
            'university_id' => $uni->id,
            'status' => 'active',
        ]);

        // Seed an existing contact under it
        $existingContact = ContactPerson::create([
            'college_id' => $college->id,
            'designation_id' => $designation->id,
            'department' => 'Computer Science',
            'name' => 'Dr. Initial Name',
            'email' => 'initial@example.com',
            'mobile' => '1111111111',
            'status' => 'active'
        ]);

        // 2. Define import rows simulating combined records
        $rows = [
            // Row 1: Completely new college & contact
            [
                'college_name' => 'New Horizon College',
                'type' => 'Autonomous',
                'affiliated_university' => 'State Technical University',
                'contact_name' => 'Prof. Alice Green',
                'designation' => 'Principal',
                'department' => 'Information Technology',
                'mobile' => '9999999999',
                'email' => 'alice@example.com'
            ],
            // Row 2: Match existing college (typo & suffix), new contact
            [
                'college_name' => 'ACE collge of Engineering, Pune',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
                'contact_name' => 'Prof. Bob Blue',
                'designation' => 'HOD', // new designation, should auto-create
                'department' => 'Mechanical Engineering',
                'mobile' => '8888888888',
                'email' => 'bob@example.com'
            ],
            // Row 3: Match existing college & existing contact -> should update details
            [
                'college_name' => 'ACE College of Engineering',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
                'contact_name' => 'Dr. Updated Name',
                'designation' => 'Principal',
                'department' => 'Computer Science',
                'mobile' => '7777777777',
                'email' => 'updated@example.com'
            ]
        ];

        // 3. Process rows using UnifiedImport
        $import = new UnifiedImport;
        foreach ($rows as $row) {
            $import->model($row);
        }

        // 4. Verify counts & status
        // New contacts created: Alice Green (Row 1), Bob Blue (Row 2) -> 2 new contact records
        // Updated contacts: Dr. Updated Name (Row 3) -> 1 updated contact record
        $this->assertEquals(2, $import->getRowCount(), 'Exactly 2 new contacts should have been created');
        $this->assertEquals(1, $import->getUpdatedCount(), 'Exactly 1 contact should have been updated');

        // Verify college count: 1 seeded + 1 new (New Horizon College) = 2 colleges total
        $this->assertEquals(2, College::count(), 'Exactly 2 colleges total should exist in the database');
        
        // Verify HOD designation was auto-created
        $this->assertDatabaseHas('designations', ['name' => 'HOD']);

        // Verify Contact details update
        $existingContact->refresh();
        $this->assertEquals('Dr. Updated Name', $existingContact->name);
        $this->assertEquals('updated@example.com', $existingContact->email);
        $this->assertEquals('7777777777', $existingContact->mobile);
    }
}
