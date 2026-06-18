<?php

namespace App\Exports;

use App\Models\University;
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
        // Provide one sample row
        return [
            [
                'Example College of Technology',
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
            'type',
            'university_name'
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

                // 3. Populate Active Universities into Column B
                $universities = University::where('status', 'active')->orderBy('name')->pluck('name')->toArray();
                foreach ($universities as $index => $uniName) {
                    $validationSheet->setCellValue('B' . ($index + 1), $uniName);
                }

                // Hide the validation lists sheet so it doesn't clutter the workbook interface
                $validationSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                // 5. Apply Data Validation dropdowns to cells from Row 2 to Row 150
                $typeValidationRange = 'ValidationLists!$A$1:$A$' . count($types);
                $uniValidationRange = 'ValidationLists!$B$1:$B$' . max(1, count($universities));

                for ($row = 2; $row <= 150; $row++) {
                    // Type validation dropdown (Column B)
                    $typeValidation = $sheet->getCell('B' . $row)->getDataValidation();
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

                    // University validation dropdown (Column C)
                    if (count($universities) > 0) {
                        $uniValidation = $sheet->getCell('C' . $row)->getDataValidation();
                        $uniValidation->setType(DataValidation::TYPE_LIST);
                        $uniValidation->setErrorStyle(DataValidation::STYLE_STOP);
                        $uniValidation->setAllowBlank(true);
                        $uniValidation->setShowInputMessage(true);
                        $uniValidation->setShowErrorMessage(true);
                        $uniValidation->setShowDropDown(true);
                        $uniValidation->setErrorTitle('Invalid University');
                        $uniValidation->setError('Please select an existing university from the dropdown.');
                        $uniValidation->setPromptTitle('Select University');
                        $uniValidation->setPrompt('Select university matching dynamic DB masters');
                        $uniValidation->setFormula1($uniValidationRange);
                    }
                }
            }
        ];
    }
}
