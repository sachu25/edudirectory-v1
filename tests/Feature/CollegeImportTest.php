<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\College;
use App\Imports\CollegeImport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollegeImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_college_import_prevents_and_detects_duplicates()
    {
        // 1. Seed some initial colleges in the DB
        $uni = \App\Models\University::create([
            'name' => 'State Technical University',
            'status' => 'active',
        ]);

        $college1 = College::create([
            'name' => 'ACE College of Engineering',
            'is_university' => false,
            'type' => 'Affiliated',
            'university_id' => $uni->id,
            'status' => 'active',
        ]);

        $college2 = College::create([
            'name' => 'Abeda Inamdar Senior College',
            'is_university' => false,
            'type' => '',
            'university_id' => $uni->id,
            'status' => 'active',
        ]);

        $college3 = College::create([
            'name' => 'Aarupadai veedu Institute of Technology',
            'is_university' => false,
            'type' => 'Affiliated',
            'status' => 'active',
        ]);

        $college4 = College::create([
            'name' => 'Al-Ameen Engineering College',
            'is_university' => false,
            'type' => 'Affiliated',
            'status' => 'active',
        ]);

        // 2. Define rows simulating importing with variations
        $rows = [
            // Exact same: should update (not duplicate)
            [
                'college_name' => 'ACE College of Engineering',
                'state' => 'kerala',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // Suffix with location (comma): should match college1
            [
                'college_name' => 'ACE College of Engineering, Thiruvananthapuram',
                'state' => 'Kerala',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // Suffix with location (space): should match college1
            [
                'college_name' => 'ACE College of Engineering Thiruvallam',
                'state' => 'Kerala',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // Spelling typo (collge): should match college1
            [
                'college_name' => 'ACE collge of Engineering',
                'state' => 'Kerala',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // Case and punctuation: should match college2
            [
                'college_name' => 'ABEDA INAMDAR SENIOR COLLEGE OF ARTS, SCIENCE & COMMERCE (AUTONOMOUS)',
                'state' => 'MAHARASHTRA',
                'type' => 'Autonomous',
                'affiliated_university' => 'State Technical University',
            ],
            // Long suffix: should match college3
            [
                'college_name' => 'Aarupadai veedu Institute of Technology , Vinayaka Mission\'s Research Foundation',
                'state' => 'tamilnadu',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // Match Al-Ameen with no hyphen
            [
                'college_name' => 'Al Ameen Engineering College',
                'state' => 'Kerala',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // Should NOT match Al-Ameen, should create a new college
            [
                'college_name' => 'Al-Asma School, Chattergam',
                'state' => 'Jammu and Kashmir',
                'type' => 'Affiliated',
                'affiliated_university' => 'State Technical University',
            ],
            // New college (which is a University): should be auto-detected as is_university = true
            [
                'college_name' => 'New Horizon University',
                'state' => 'Karnataka',
                'type' => 'Autonomous',
                'affiliated_university' => '',
            ],
        ];

        // 3. Run the import model method row by row
        $import = new CollegeImport;
        
        foreach ($rows as $row) {
            $import->model($row);
        }

        // 4. Verify the counts
        // 2 new colleges created (Al-Asma School, New Horizon University)
        // 9 rows processed: 7 matched existing (updated), 2 created
        $this->assertEquals(2, $import->getRowCount(), 'Exactly 2 new colleges should have been created');
        $this->assertEquals(7, $import->getUpdatedCount(), 'Exactly 7 colleges should have been detected as duplicates and updated');

        // Verify the database has only 6 colleges total (4 seeded + 2 new)
        $this->assertEquals(6, College::count());

        // Verify that Abeda Inamdar's type was updated to Autonomous
        $this->assertEquals('Autonomous', $college2->fresh()->type);

        // Verify that New Horizon University was auto-detected as a university
        $newUni = College::where('name', 'New Horizon University')->first();
        $this->assertNotNull($newUni);
        $this->assertTrue($newUni->is_university);

        // Verify state field normalization
        $this->assertEquals('Kerala', $college1->fresh()->state);
        $this->assertEquals('Maharashtra', $college2->fresh()->state);
        $this->assertEquals('Tamil Nadu', $college3->fresh()->state);
    }
}
