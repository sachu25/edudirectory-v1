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
                'APJ Abdul Kalam Technological University',
                'No'
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
            'affiliated_university',
            'fdp_client'
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

                // 2. Populate College Types into Column A, FDP options into Column B
                $types = ['Affiliated', 'Autonomous', 'Constituent', 'Deemed', 'Other'];
                foreach ($types as $index => $type) {
                    $validationSheet->setCellValue('A' . ($index + 1), $type);
                }

                $fdpOptions = ['Yes', 'No'];
                foreach ($fdpOptions as $index => $opt) {
                    $validationSheet->setCellValue('B' . ($index + 1), $opt);
                }

                // Hide the validation lists sheet
                $validationSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                // Apply Data Validation dropdowns to cells from Row 2 to Row 150
                $typeValidationRange = 'ValidationLists!$A$1:$A$' . count($types);
                $fdpValidationRange = 'ValidationLists!$B$1:$B$' . count($fdpOptions);

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

                    // FDP Client validation dropdown (Column E)
                    $fdpValidation = $sheet->getCell('E' . $row)->getDataValidation();
                    $fdpValidation->setType(DataValidation::TYPE_LIST);
                    $fdpValidation->setErrorStyle(DataValidation::STYLE_STOP);
                    $fdpValidation->setAllowBlank(true);
                    $fdpValidation->setShowInputMessage(true);
                    $fdpValidation->setShowErrorMessage(true);
                    $fdpValidation->setShowDropDown(true);
                    $fdpValidation->setErrorTitle('Invalid Selection');
                    $fdpValidation->setError('Please select Yes or No.');
                    $fdpValidation->setPromptTitle('Is FDP Client?');
                    $fdpValidation->setPrompt('Yes, No');
                    $fdpValidation->setFormula1($fdpValidationRange);
                }
            }
        ];
    }
}
