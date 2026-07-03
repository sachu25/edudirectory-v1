<?php

namespace App\Exports;

use App\Models\ContactPerson;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContactExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?? request();
    }

    public function collection()
    {
        $query = ContactPerson::with(['college', 'designation']);

        if ($this->request->filled('college_id')) {
            $query->where('college_id', $this->request->college_id);
        }

        if ($this->request->filled('designation_id')) {
            $query->where('designation_id', $this->request->designation_id);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhereHas('college', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('designation', function($d) use ($search) {
                      $d->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Handle sorting
        $sortColumn = $this->request->sort_column;
        $sortDirection = $this->request->sort_direction;

        if ($sortColumn && in_array($sortDirection, ['asc', 'desc'])) {
            if ($sortColumn === 'college.name') {
                $query->orderBy(
                    \App\Models\College::select('name')
                        ->whereColumn('colleges.id', 'contact_persons.college_id'),
                    $sortDirection
                );
            } elseif ($sortColumn === 'designation.name') {
                $query->orderBy(
                    \App\Models\Designation::select('name')
                        ->whereColumn('designations.id', 'contact_persons.designation_id'),
                    $sortDirection
                );
            } elseif (in_array($sortColumn, ['name', 'department', 'mobile', 'status'])) {
                $query->orderBy($sortColumn, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Institution Name',
            'Contact Name',
            'Designation',
            'Department',
            'Mobile',
            'WhatsApp',
            'Email',
            'Status',
            'Is Primary',
            'Created At'
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->college ? $contact->college->name : 'N/A',
            $contact->name,
            $contact->designation ? $contact->designation->name : 'N/A',
            $contact->department ?? 'N/A',
            $contact->mobile ?? 'N/A',
            $contact->whatsapp ?? 'N/A',
            $contact->email ?? 'N/A',
            ucfirst($contact->status),
            $contact->is_priority ? 'Yes' : 'No',
            $contact->created_at ? $contact->created_at->format('Y-m-d') : 'N/A'
        ];
    }
}
