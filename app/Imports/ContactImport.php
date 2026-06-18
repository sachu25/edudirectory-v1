<?php

namespace App\Imports;

use App\Models\ContactPerson;
use App\Models\College;
use App\Models\Designation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ContactImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $rowsCount = 0;
    private $updatedCount = 0;
    private $collegesCache = [];
    private $designationsCache = [];
    private $currentRowIndex = 1;
    public $skippedRows = [];

    private $collegesNormalizedMap = null;

    private function normalizeCollegeName(string $name): string
    {
        $name = strtolower($name);
        $name = str_replace('autonomous', '', $name);
        return preg_replace('/[^a-z0-9]/', '', $name);
    }

    private function initCollegesMap()
    {
        if ($this->collegesNormalizedMap === null) {
            $this->collegesNormalizedMap = [];
            $colleges = College::select('id', 'name')->get();
            foreach ($colleges as $col) {
                $norm = $this->normalizeCollegeName($col->name);
                $this->collegesNormalizedMap[$norm] = $col;
            }
        }
    }

    public function model(array $row)
    {
        $this->currentRowIndex++;
        $this->initCollegesMap();

        // 1. Query College by name (using in-memory cache)
        $collegeName = isset($row['college_name']) ? trim($row['college_name']) : '';
        $normName = $this->normalizeCollegeName($collegeName);
        if (empty($normName)) {
            $this->skippedRows[] = [
                'row' => $this->currentRowIndex,
                'name' => $row['contact_name'] ?? 'N/A',
                'reason' => 'College name is empty.'
            ];
            return null;
        }

        if (array_key_exists($normName, $this->collegesNormalizedMap)) {
            $college = $this->collegesNormalizedMap[$normName];
        } else {
            $college = null;
        }

        if (!$college) {
            $this->skippedRows[] = [
                'row' => $this->currentRowIndex,
                'name' => $row['contact_name'] ?? 'N/A',
                'reason' => "College '{$collegeName}' was not found in the database. Please import the college first."
            ];
            return null;
        }

        // 2. Query Designation by name, auto-create if missing (using in-memory cache)
        $designationName = isset($row['designation']) ? trim($row['designation']) : '';
        $designationLower = strtolower($designationName);
        if (empty($designationLower)) {
            $this->skippedRows[] = [
                'row' => $this->currentRowIndex,
                'name' => $row['contact_name'] ?? 'N/A',
                'reason' => 'Designation name is empty.'
            ];
            return null;
        }

        if (array_key_exists($designationLower, $this->designationsCache)) {
            $designation = $this->designationsCache[$designationLower];
        } else {
            $designation = Designation::whereRaw('LOWER(name) = ?', [$designationLower])->first();
            if (!$designation) {
                $designation = Designation::create([
                    'name' => $designationName,
                    'status' => 'active'
                ]);
            }
            $this->designationsCache[$designationLower] = $designation;
        }

        // 3. Normalize department
        $department = isset($row['department']) ? trim($row['department']) : null;
        if ($department === '') {
            $department = null;
        }

        // 4. Check for existing contact person with matching college, designation, and department
        $query = ContactPerson::where('college_id', $college->id)
            ->where('designation_id', $designation->id);
        
        if ($department === null) {
            $query->whereNull('department');
        } else {
            $query->whereRaw('LOWER(department) = ?', [strtolower($department)]);
        }

        $existingContact = $query->first();

        $contactName = isset($row['contact_name']) ? trim($row['contact_name']) : '';
        $email = isset($row['email']) ? trim($row['email']) : null;
        $mobile = isset($row['mobile']) ? trim($row['mobile']) : null;

        if ($existingContact) {
            $existingContact->update([
                'name' => $contactName,
                'email' => $email,
                'mobile' => $mobile,
            ]);
            $this->updatedCount++;
            return null;
        }

        $this->rowsCount++;

        // Create new contact person
        return new ContactPerson([
            'college_id' => $college->id,
            'designation_id' => $designation->id,
            'department' => $department,
            'name' => $contactName,
            'email' => $email,
            'mobile' => $mobile,
            'status' => 'active',
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rowsCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function isEmptyWhen(array $row): bool
    {
        $collegeName = isset($row['college_name']) ? trim($row['college_name']) : '';
        $contactName = isset($row['contact_name']) ? trim($row['contact_name']) : '';
        $designation = isset($row['designation']) ? trim($row['designation']) : '';

        return $collegeName === '' && $contactName === '' && $designation === '';
    }

    public function rules(): array
    {
        return [
            'college_name' => 'required|string',
            'contact_name' => 'required|string',
            'designation' => 'required|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable',
            'email' => 'nullable|email',
        ];
    }
}
