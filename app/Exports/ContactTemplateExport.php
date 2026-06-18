<?php

namespace App\Exports;

use App\Models\College;
use App\Models\Designation;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ContactTemplateExport implements FromArray, WithHeadings, WithEvents
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                'Example College of Technology',
                'John Doe',
                'HOD',
                'Computer Science',
                '9876543210',
                'johndoe@example.com'
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'college_name',
            'contact_name',
            'designation',
            'department',
            'mobile',
            'email'
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $workbook = $event->sheet->getParent();

                // 1. Create a validation sheet to store lists
                $validationSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($workbook, 'ValidationLists');
                $workbook->addSheet($validationSheet);

                // 2. Populate Colleges into Column A of the validation sheet
                $colleges = College::orderBy('name')->pluck('name')->toArray();
                foreach ($colleges as $index => $collegeName) {
                    $validationSheet->setCellValue('A' . ($index + 1), $collegeName);
                }

                // 3. Populate Designations into Column B
                $designations = Designation::where('status', 'active')->orderBy('name')->pluck('name')->toArray();
                foreach ($designations as $index => $desName) {
                    $validationSheet->setCellValue('B' . ($index + 1), $desName);
                }

                // Hide the validation lists sheet
                $validationSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                // Apply Data Validation dropdowns to cells from Row 2 to Row 150
                $collegeValidationRange = 'ValidationLists!$A$1:$A$' . max(1, count($colleges));
                $designationValidationRange = 'ValidationLists!$B$1:$B$' . max(1, count($designations));

                for ($row = 2; $row <= 150; $row++) {
                    // College validation dropdown (Column A)
                    if (count($colleges) > 0) {
                        $collegeValidation = $sheet->getCell('A' . $row)->getDataValidation();
                        $collegeValidation->setType(DataValidation::TYPE_LIST);
                        $collegeValidation->setErrorStyle(DataValidation::STYLE_STOP);
                        $collegeValidation->setAllowBlank(true);
                        $collegeValidation->setShowInputMessage(true);
                        $collegeValidation->setShowErrorMessage(true);
                        $collegeValidation->setShowDropDown(true);
                        $collegeValidation->setErrorTitle('Invalid College');
                        $collegeValidation->setError('Please select an existing college from the dropdown.');
                        $collegeValidation->setPromptTitle('Select College');
                        $collegeValidation->setPrompt('Select dynamic DB colleges');
                        $collegeValidation->setFormula1($collegeValidationRange);
                    }

                    // Designation validation dropdown (Column C)
                    if (count($designations) > 0) {
                        $desValidation = $sheet->getCell('C' . $row)->getDataValidation();
                        $desValidation->setType(DataValidation::TYPE_LIST);
                        $desValidation->setErrorStyle(DataValidation::STYLE_INFORMATION); // Info style because we can auto-create if missing
                        $desValidation->setAllowBlank(true);
                        $desValidation->setShowInputMessage(true);
                        $desValidation->setShowErrorMessage(false);
                        $desValidation->setShowDropDown(true);
                        $desValidation->setPromptTitle('Select Designation');
                        $desValidation->setPrompt('Select designation or enter a new one (will be created)');
                        $desValidation->setFormula1($designationValidationRange);
                    }
                }
            }
        ];
    }
}
