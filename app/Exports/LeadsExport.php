<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $leads;
    
    public function __construct(array $leads)
    {
        $this->leads = $leads;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'First Name', 
            'Last Name', 
            'Country', 
            'Address',
            'Date Oppd In', 
            'Campaign Product',
            'Sdm',
            'Date of Birth',
            'Occupation',
            'Agents Book',
            'Account Manager',
            'Vc', 
            'Data Type',
            'Data Source',
            'Data Code',
            'Email', 
            'Email Alt 1',
            'Email Alt 2',
            'Email Alt 3',
            'Phone Number',
            'Phone Number Alt 1',
            'Phone Number Alt 2',
            'Phone Number Alt 3',
            'Private Remark',
            'Remark',
            'Appointment Start At',
            'Appointment End At',
            'Last Called', 
            'Assignee Read At',
            'Give Up At', 
            'Appointment Label',
            'Contact Outcome',
            'Stage',
            'Assignee', 
            'Created By',
            'Delete At',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function query()
    {
        // dd(gettype($this->leads));
        return Lead::query()->whereIn('id', $this->leads);
    }
}
