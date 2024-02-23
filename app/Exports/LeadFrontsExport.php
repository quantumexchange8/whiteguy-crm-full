<?php

namespace App\Exports;

use App\Models\LeadFront;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadFrontsExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $leadFronts;
    
    public function __construct(array $leadFronts)
    {
        $this->leadFronts = $leadFronts;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'Name', 
            'Assignee', 
            'Product', 
            'Quantity', 
            'Price', 
            'Total', 
            'Commission', 
            'Vc', 
            'Sdm', 
            'Liquid', 
            'Mimo', 
            'Bank Name', 
            'Bank Account', 
            'Note', 
            'Linked Lead',
            'Edited At', 
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function query()
    {
        return LeadFront::query()->whereIn('id', $this->leadFronts);
    }
}
