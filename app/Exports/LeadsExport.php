<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
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
            'Date Oppd In', 
            'First Name', 
            'Last Name', 
            'Country', 
            'Date of Birth',
            'Occupation',
            'Vc', 
            'Sdm',
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
            'Data Source',
            'Appointment Start At',
            'Appointment End At',
            'Last Called', 
            'Assignee Read At',
            'Edited At',
            'Created At',
            'Appointment Label',
            'Assignee', 
            'Contact Outcome',
            'Created By',
            'Stage',
            'Give Up At', 
            'Account Manager',
            'Address',
            'Agents Book',
            'Campaign Product',
            'Data Code',
            'Data Type',
            'Deleted At',
            'Deleted Note',
            // 'Sort ID',
        ];
    }

    /**
    * @param Lead $lead
    */
    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->date,
            $lead->first_name,
            $lead->last_name,
            $lead->country,
            $lead->date_of_birth,
            $lead->occupation,
            $lead->vc,
            $lead->sdm,
            $lead->email,
            $lead->email_alt_1,
            $lead->email_alt_2,
            $lead->email_alt_3,
            $lead->phone_number,
            $lead->phone_number_alt_1,
            $lead->phone_number_alt_2,
            $lead->phone_number_alt_3,
            $lead->private_remark,
            $lead->remark,
            $lead->data_source,
            $lead->appointment_start_at,
            $lead->appointment_end_at,
            $lead->contacted_at,
            $lead->assignee_read_at,
            $lead->edited_at,
            $lead->created_at,
            $lead->appointment_label_id ? $lead->appointmentLabel->title : '',
            $lead->assignee_id ? $lead->assignee->username . ' (' . $lead->assignee->site->name . ')' : 'No Assignee Set',
            $lead->contact_outcome_id ? $lead->contactOutcome->title : '',
            $lead->created_by_id ? $lead->leadCreator->username . ' (' . $lead->leadCreator->site->name . ')' : 'No Creator Set',
            $lead->stage_id ? $lead->stage->title : '',
            $lead->give_up_at,
            $lead->account_manager,
            $lead->address,
            $lead->agents_book,
            $lead->campaign_product,
            $lead->data_code,
            $lead->data_type,
            $lead->deleted_at,
            $lead->deleted_note,
            // $lead->sort_id,
        ];
    }

    public function query()
    {
        return Lead::query()
                        ->with([
                                    'assignee:id,username,site_id',
                                    'assignee.site:id,name',
                                    'leadCreator:id,username,site_id',
                                    'leadCreator.site:id,name',
                                    'stage:id,title',
                                    'contactOutcome:id,title',
                                    'appointmentLabel:id,title'
                                ])
                        ->orderByDesc('id')
                        ->whereIn('id', $this->leads);
    }
}
