<?php

namespace App\Exports;

use App\Models\LeadFront;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadFrontsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
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
            'Email',
            'Phone Number',
            'Mimo', 
            'Product', 
            'Quantity', 
            'Price', 
            'Total', 
            'Agent Commission', 
            'Vc', 
            'Sdm', 
            'Liquid', 
            'Bank', 
            'Note', 
            'Assignee', 
            'Linked Lead',
            'Edited At', 
            'Created At',
        ];
    }

    /**
    * @param LeadFront $leadFront
    */
    public function map($leadFront): array
    {
        return [
            $leadFront->id,
            $leadFront->name,
            $leadFront->email,
            $leadFront->phone_number,
            $leadFront->mimo,
            $leadFront->product,
            number_format(((float)$leadFront->quantity), 2, '.', ''),
            number_format(((float)$leadFront->price), 2, '.', ''),
            number_format(((float)$leadFront->price * (float)$leadFront->quantity), 2, '.', ''),
            '(' . number_format((float)$leadFront->commission, 2, '.', '') . '%) ' . number_format(((float)$leadFront->commission / 100 * ((float)$leadFront->price * (float)$leadFront->quantity)), 2, '.', ''),
            $leadFront->vc,
            (!$leadFront->sdm) ? 'FALSE' : $leadFront->sdm,
            (!$leadFront->liquid) ? 'FALSE' : $leadFront->liquid,
            $leadFront->bank,
            $leadFront->note,
            $leadFront->lead?->assignee ? $leadFront->lead->assignee->username . ' (' . $leadFront->lead->assignee->site->name . ')' : 'No Assignee Set',
            $leadFront->lead ? $leadFront->lead->first_name . ' ' . $leadFront->lead->last_name : 'No Linked Lead Set',
            $leadFront->edited_at,
            $leadFront->created_at,
        ];
    }

    public function query()
    {
        return LeadFront::query()
                            ->with([
                                        'lead:id,first_name,last_name,assignee_id', 
                                        'lead.assignee:id,username,site_id',
                                        'lead.assignee.site:id,name'
                                    ])
                            ->whereIn('id', $this->leadFronts)
                            ->orderByDesc('id');
    }
}
