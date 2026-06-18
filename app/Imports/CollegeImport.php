<?php

namespace App\Imports;

use App\Models\College;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\University;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CollegeImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $rowsCount = 0;
    private $updatedCount = 0;
    private $universitiesCache = [];

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
            $colleges = College::select('id', 'name', 'type', 'university_id')->get();
            foreach ($colleges as $col) {
                $norm = $this->normalizeCollegeName($col->name);
                $this->collegesNormalizedMap[$norm] = $col;
            }
        }
    }

    public function model(array $row)
    {
        $this->initCollegesMap();

        $uniName = isset($row['university_name']) ? trim($row['university_name']) : '';
        $uniLower = strtolower($uniName);
        $universityId = null;

        if (!empty($uniLower)) {
            if (array_key_exists($uniLower, $this->universitiesCache)) {
                $universityId = $this->universitiesCache[$uniLower];
            } else {
                $university = University::where('name', $uniName)
                                ->orWhere('short_name', $uniName)
                                ->first();
                $universityId = $university ? $university->id : null;
                $this->universitiesCache[$uniLower] = $universityId;
            }
        }
                        
        $collegeName = isset($row['college_name']) ? trim($row['college_name']) : '';
        $normName = $this->normalizeCollegeName($collegeName);

        if (array_key_exists($normName, $this->collegesNormalizedMap)) {
            $existingCollege = $this->collegesNormalizedMap[$normName];
            $existingCollege->update([
                'type' => isset($row['type']) ? trim($row['type']) : $existingCollege->type,
                'university_id' => $universityId ?? $existingCollege->university_id,
            ]);
            $this->updatedCount++;
            return null;
        }

        $college = College::create([
            'name'             => $collegeName,
            'type'             => isset($row['type']) ? trim($row['type']) : null,
            'university_id'    => $universityId,
            'status'           => 'active',
        ]);

        $this->collegesNormalizedMap[$normName] = $college;
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
            'type' => 'nullable|string',
            'university_name' => 'nullable|string',
        ];
    }
}

