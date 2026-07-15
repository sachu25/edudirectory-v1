<?php

namespace App\Imports;

use App\Models\College;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\University;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class CollegeImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private $rowsCount = 0;
    private $updatedCount = 0;
    private $collegesCache = null;

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

            // 2. Prefix / Subset match: check if one normalized name contains or starts with the other
            // and the mismatch is relatively small (location suffix)
            $lenInput = strlen($normInput['full']);
            $lenExisting = strlen($normExisting['full']);
            if ($lenInput > 0 && $lenExisting > 0) {
                if (strpos($normInput['full'], $normExisting['full']) === 0 || strpos($normExisting['full'], $normInput['full']) === 0) {
                    // Ensure the shorter one is a significant part of the longer one to avoid matching completely different names
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
        $affiliatedUniversity = isset($row['affiliated_university']) ? trim($row['affiliated_university']) : '';
                        
        $collegeName = isset($row['college_name']) ? trim($row['college_name']) : '';
        $state = isset($row['state']) ? College::sanitizeState($row['state']) : '';
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

        $fdpClient = isset($row['fdp_client']) ? (in_array(strtolower(trim($row['fdp_client'])), ['yes', 'y', '1', 'true']) ? 'Yes' : 'No') : 'No';

        $existingCollege = $this->findExistingDuplicate($normInput);

        if ($existingCollege) {
            $existingCollege->update([
                'type' => $existingCollege->type ?: $type,
                'university_id' => $universityId ?: $existingCollege->university_id,
                'is_university' => $existingCollege->is_university || $isUniversity,
                'state' => $existingCollege->state ?: $state,
                'fdp_client' => $existingCollege->fdp_client === 'Yes' ? 'Yes' : $fdpClient,
            ]);
            $this->updatedCount++;
            return null;
        }

        $college = College::create([
            'name'                  => $collegeName,
            'is_university'         => $isUniversity,
            'type'                  => $type,
            'university_id'         => $universityId,
            'state'                 => $state,
            'status'                => 'active',
            'fdp_client'            => $fdpClient,
        ]);

        if ($this->collegesCache !== null) {
            $this->collegesCache[] = [
                'model' => $college,
                'normalized' => $normInput
            ];
        }

        $this->rowsCount++;
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
        
        return $collegeName === '';
    }

    public function rules(): array
    {
        return [
            'college_name' => 'required|string',
            'state' => 'required|string',
            'type' => 'nullable|string',
            'affiliated_university' => 'nullable|string',
            'fdp_client' => 'nullable|string',
        ];
    }
}

