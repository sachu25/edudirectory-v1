<?php

namespace App\Exports;

use App\Models\College;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CollegeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?? request();
    }

    public function collection()
    {
        $query = College::query();

        if ($this->request->filled('university_id')) {
            $query->where('university_id', $this->request->university_id);
        }
        if ($this->request->filled('type')) {
            $query->where('type', $this->request->type);
        }
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->filled('state')) {
            $query->where('state', $this->request->state);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Institution Name',
            'Is University',
            'Code',
            'Type',
            'Affiliated University',
            'NAAC Grade',
            'NIRF Ranking',
            'Established Year',
            'Official Email',
            'Website',
            'Office Phone',
            'Office Mobile',
            'Student Strength',
            'Faculty Strength',
            'District',
            'State',
            'Status',
            'Created At'
        ];
    }

    public function map($college): array
    {
        return [
            $college->id,
            $college->name,
            $college->is_university ? 'Yes' : 'No',
            $college->code,
            $college->type,
            $college->university ? $college->university->name : 'N/A',
            $college->naac_grade,
            $college->nirf_ranking,
            $college->established_year,
            $college->official_email,
            $college->website,
            $college->office_phone,
            $college->office_mobile,
            $college->student_strength,
            $college->faculty_strength,
            $college->district,
            $college->state,
            ucfirst($college->status),
            $college->created_at->format('Y-m-d')
        ];
    }
}
