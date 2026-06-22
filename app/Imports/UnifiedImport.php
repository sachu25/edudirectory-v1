<?php

namespace App\Imports;

use App\Models\College;
use App\Models\ContactPerson;
use App\Models\Designation;
use App\Models\University;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UnifiedImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $rowsCount = 0;
    private $updatedCount = 0;
    
    private $collegesCache = null;
    private $designationsCache = [];
    private $currentRowIndex = 1;
    public $skippedRows = [];

    public function normalizeCollegeName(string $name): array
    {
        $original = trim($name);
        $lower = strtolower($original);

        // 1. Extract strict base name (before comma, space-dash-space, parenthesis, or brackets)
        // This avoids splitting hyphenated names like Al-Ameen while still handling "College - Location"
        $parts = preg_split('/,|[\(\\[]|\s+-\s+/', $lower);
        $basePart = trim($parts[0]);
        if (strlen($basePart) < 3) {
            $basePart = $lower;
        }

        // Helper to perform deep cleaning of a string
        $clean = function(string $str) {
            // Replace common abbreviations
            $abbreviations = [
                'collge' => 'college',
                'clg' => 'college',
                'engg' => 'engineering',
                'tech' => 'technology',
                'inst' => 'institute',
                'univ' => 'university',
                'dept' => 'department',
            ];
            
            $words = preg_split('/\s+/', $str);
            foreach ($words as &$word) {
                if (isset($abbreviations[$word])) {
                    $word = $abbreviations[$word];
                }
            }
            $str = implode(' ', $words);

            // Replace & with and
            $str = str_replace('&', 'and', $str);

            // Remove noise words
            $noiseWords = ['autonomous', 'deemed', 'university', 'college of', 'institute of', 'college', 'institute'];
            foreach ($noiseWords as $noise) {
                $str = str_replace($noise, '', $str);
            }

            // Remove all non-alphanumeric characters
            return preg_replace('/[^a-z0-9]/', '', $str);
        };

        return [
            'strict' => $clean($basePart),
            'full' => $clean($lower),
            'original' => $original
        ];
    }

    public function findExistingDuplicate(array $normInput)
    {
        if ($this->collegesCache === null) {
            $colleges = College::select('id', 'name', 'type', 'is_university', 'university_id')->get();
            $this->collegesCache = [];
            foreach ($colleges as $college) {
                $this->collegesCache[] = [
                    'model' => $college,
                    'normalized' => $this->normalizeCollegeName($college->name)
                ];
            }
        }
        
        foreach ($this->collegesCache as $cached) {
            $college = $cached['model'];
            $normExisting = $cached['normalized'];
            
            // 1. Exact match on strict base name or full name
            if ($normInput['strict'] === $normExisting['strict'] || $normInput['full'] === $normExisting['full']) {
                return $college;
            }

            // 2. Prefix / Subset match
            $lenInput = strlen($normInput['full']);
            $lenExisting = strlen($normExisting['full']);
            if ($lenInput > 0 && $lenExisting > 0) {
                if (strpos($normInput['full'], $normExisting['full']) === 0 || strpos($normExisting['full'], $normInput['full']) === 0) {
                    $minLen = min($lenInput, $lenExisting);
                    $maxLen = max($lenInput, $lenExisting);
                    if ($minLen / $maxLen >= 0.5) {
                        return $college;
                    }
                }
            }

            // 2b. Strict Prefix / Subset match
            $lenStrictInput = strlen($normInput['strict']);
            $lenStrictExisting = strlen($normExisting['strict']);
            if ($lenStrictInput > 0 && $lenStrictExisting > 0) {
                if (strpos($normInput['strict'], $normExisting['strict']) === 0 || strpos($normExisting['strict'], $normInput['strict']) === 0) {
                    $minStrictLen = min($lenStrictInput, $lenStrictExisting);
                    $maxStrictLen = max($lenStrictInput, $lenStrictExisting);
                    if ($minStrictLen / $maxStrictLen >= 0.5) {
                        return $college;
                    }
                }
            }

            // 3. Fuzzy similarity matching using Levenshtein distance
            if ($lenInput > 0 && $lenExisting > 0) {
                $lev = levenshtein($normInput['full'], $normExisting['full']);
                $maxLen = max($lenInput, $lenExisting);
                $similarity = (1 - ($lev / $maxLen)) * 100;
                
                if ($similarity >= 85.0) {
                    return $college;
                }
            }
        }

        return null;
    }

    public function model(array $row)
    {
        $this->currentRowIndex++;

        // --- 1. PROCESS COLLEGE ---
        $affiliatedUniversity = isset($row['affiliated_university']) ? trim($row['affiliated_university']) : '';
                        
        $collegeName = isset($row['college_name']) ? trim($row['college_name']) : '';
        if (empty($collegeName)) {
            $this->skippedRows[] = [
                'row' => $this->currentRowIndex,
                'name' => $row['contact_name'] ?? 'N/A',
                'reason' => 'College name is empty.'
            ];
            return null;
        }

        $normInput = $this->normalizeCollegeName($collegeName);

        $type = isset($row['type']) ? trim($row['type']) : '';
        if (empty($type)) {
            if (stripos($collegeName, 'Autonomous') !== false) {
                $type = 'Autonomous';
            } else {
                $type = 'Affiliated';
            }
        }

        $isUniversity = (stripos($collegeName, 'university') !== false) ? 1 : 0;

        $universityId = null;
        if (!empty($affiliatedUniversity)) {
            $uni = University::firstOrCreate(
                ['name' => $affiliatedUniversity],
                ['status' => 'active']
            );
            $universityId = $uni->id;
        }

        $college = $this->findExistingDuplicate($normInput);

        if ($college) {
            $college->update([
                'type' => $college->type ?: $type,
                'university_id' => $universityId ?: $college->university_id,
                'is_university' => $college->is_university || $isUniversity,
            ]);
        } else {
            $college = College::create([
                'name'                  => $collegeName,
                'is_university'         => $isUniversity,
                'type'                  => $type,
                'university_id'         => $universityId,
                'status'                => 'active',
            ]);

            if ($this->collegesCache !== null) {
                $this->collegesCache[] = [
                    'model' => $college,
                    'normalized' => $normInput
                ];
            }
        }

        // --- 2. PROCESS CONTACT ---
        $contactName = isset($row['contact_name']) ? trim($row['contact_name']) : '';
        if (empty($contactName)) {
            // If contact details are empty, we just skip creating a contact but college was processed
            return null;
        }

        $designationName = isset($row['designation']) ? trim($row['designation']) : '';
        $designationLower = strtolower($designationName);
        if (empty($designationLower)) {
            $this->skippedRows[] = [
                'row' => $this->currentRowIndex,
                'name' => $contactName,
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

        $department = isset($row['department']) ? trim($row['department']) : null;
        if ($department === '') {
            $department = null;
        }

        $email = isset($row['email']) ? trim($row['email']) : null;
        $mobile = isset($row['mobile']) ? trim($row['mobile']) : null;

        // Check if contact person already exists under that college
        $query = ContactPerson::where('college_id', $college->id)
            ->where('designation_id', $designation->id);
        
        if ($department === null) {
            $query->whereNull('department');
        } else {
            $query->whereRaw('LOWER(department) = ?', [strtolower($department)]);
        }

        $existingContact = $query->first();

        if ($existingContact) {
            $existingContact->update([
                'name' => $contactName,
                'email' => $email,
                'mobile' => $mobile,
            ]);
            $this->updatedCount++;
        } else {
            ContactPerson::create([
                'college_id' => $college->id,
                'designation_id' => $designation->id,
                'department' => $department,
                'name' => $contactName,
                'email' => $email,
                'mobile' => $mobile,
                'status' => 'active',
            ]);
            $this->rowsCount++;
        }

        return null;
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

        return $collegeName === '' && $contactName === '';
    }

    public function rules(): array
    {
        return [
            'college_name' => 'required|string',
            'type' => 'nullable|string',
            'affiliated_university' => 'nullable|string',
            'contact_name' => 'required|string',
            'designation' => 'required|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable',
            'email' => 'nullable|email',
        ];
    }
}
