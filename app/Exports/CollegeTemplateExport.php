<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CollegeTemplateExport implements FromArray, WithHeadings, WithEvents
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                'Example College of Technology',
                'Kerala',
                'Autonomous',
                'APJ Abdul Kalam Technological University'
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
            'state',
            'type',
            'affiliated_university'
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

                // 2. Populate College Types into Column A of the validation sheet
                $types = ['Affiliated', 'Autonomous', 'Constituent', 'Deemed', 'Other'];
                foreach ($types as $index => $type) {
                    $validationSheet->setCellValue('A' . ($index + 1), $type);
                }

                // Hide the validation lists sheet
                $validationSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                // Apply Data Validation dropdowns to cells from Row 2 to Row 150
                $typeValidationRange = 'ValidationLists!$A$1:$A$' . count($types);

                for ($row = 2; $row <= 150; $row++) {
                    // Type validation dropdown (Column C)
                    $typeValidation = $sheet->getCell('C' . $row)->getDataValidation();
                    $typeValidation->setType(DataValidation::TYPE_LIST);
                    $typeValidation->setErrorStyle(DataValidation::STYLE_STOP);
                    $typeValidation->setAllowBlank(true);
                    $typeValidation->setShowInputMessage(true);
                    $typeValidation->setShowErrorMessage(true);
                    $typeValidation->setShowDropDown(true);
                    $typeValidation->setErrorTitle('Invalid Type');
                    $typeValidation->setError('Please select a valid type from the dropdown.');
                    $typeValidation->setPromptTitle('Select College Type');
                    $typeValidation->setPrompt('Affiliated, Autonomous, Constituent, Deemed, Other');
                    $typeValidation->setFormula1($typeValidationRange);
                }
            }
        ];
    }
}
