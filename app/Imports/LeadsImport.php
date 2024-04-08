<?php

namespace App\Imports;

use App\Models\LeadDuplicated;
use App\Models\Lead;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

// HeadingRowFormatter::extend('custom', function($value, $key) {
//     return [
//         'date',
//         'first_name', 
//         'last_name', 
//         'country', 
//         'date_of_birth',
//         'occupation',
//         'vc', 
//         'sdm',
//         'email', 
//         'email_alt_1',
//         'email_alt_2',
//         'email_alt_3',
//         'phone_number',
//         'phone_number_alt_1',
//         'phone_number_alt_2',
//         'phone_number_alt_3',
//         'attachment',
//         'private_remark',
//         'remark',
//         'data_source',
//         'appointment_start_at',
//         'appointment_end_at',
//         'contacted_at',
//         'assignee_read_at',
//         'edited_at',
//         'created_at',
//         'appointment_label_id',
//         'assignee_id', 
//         'contact_outcome_id',
//         'created_by_id',
//         'stage_id',
//         'give_up_at', 
//         'account_manager',
//         'address',
//         'agents_book',
//         'campaign_product',
//         'data_code',
//         'data_type',
//         'deleted_at',
//         'deleted_note',
//         'sort_id',
//     ];
    
//     // And you can use heading column index.
//     // return 'column-' . $key; 
// });

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function headings(): array
    {
        return [
            'date',
            'first_name', 
            'last_name', 
            'country', 
            'date_of_birth',
            'occupation',
            'vc', 
            'sdm',
            'email', 
            'email_alt_1',
            'email_alt_2',
            'email_alt_3',
            'phone_number',
            'phone_number_alt_1',
            'phone_number_alt_2',
            'phone_number_alt_3',
            'attachment',
            'private_remark',
            'remark',
            'data_source',
            'appointment_start_at',
            'appointment_end_at',
            'contacted_at',
            'assignee_read_at',
            'edited_at',
            'created_at',
            'appointment_label_id',
            'assignee_id', 
            'contact_outcome_id',
            'created_by_id',
            'stage_id',
            'give_up_at', 
            'account_manager',
            'address',
            'agents_book',
            'campaign_product',
            'data_code',
            'data_type',
            'deleted_at',
            'deleted_note',
            'sort_id',
        ];
    }

    public function model(array $row)
    {
        // Check for duplicates based on email and phone number
        if (Lead::where('email', $row['email'])->orWhere('phone_number', $row['phone_number'])->exists()) {
            // If a duplicate is found, insert the data into the duplicated_leads table
            
            // dd($row);

            LeadDuplicated::create([
                'date' => $row['date'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'country' => $row['country'],
                'date_of_birth' => $row['date_of_birth'],
                'occupation' => $row['occupation'],
                'vc' => $row['vc'],
                'sdm' => $row['sdm'],
                'email' => $row['email'],
                'email_alt_1' => $row['email_alt_1'],
                'email_alt_2' => $row['email_alt_2'],
                'email_alt_3' => $row['email_alt_3'],
                'phone_number' => $row['phone_number'],
                'phone_number_alt_1' => $row['phone_number_alt_1'],
                'phone_number_alt_2' => $row['phone_number_alt_2'],
                'phone_number_alt_3' => $row['phone_number_alt_3'],
                'attachment' => $row['attachment'],
                'private_remark' => $row['private_remark'],
                'remark' => $row['remark'],
                'data_source' => $row['data_source'],
                'appointment_start_at' => $row['appointment_start_at'],
                'appointment_end_at' => $row['appointment_end_at'],
                'contacted_at' => $row['contacted_at'],
                'assignee_read_at' => $row['assignee_read_at'],
                'edited_at' => $row['edited_at'],
                'created_at' => $row['created_at'],
                'appointment_label_id' => $row['appointment_label_id'],
                'assignee_id' => $row['assignee_id'],
                'contact_outcome_id' => $row['contact_outcome_id'],
                'created_by_id' => $row['created_by_id'],
                'stage_id' => $row['stage_id'],
                'give_up_at' => $row['give_up_at'],
                'account_manager' => $row['account_manager'],
                'address' => $row['address'],
                'agents_book' => $row['agents_book'],
                'campaign_product' => $row['campaign_product'],
                'data_code' => $row['data_code'],
                'data_type' => $row['data_type'],
                'deleted_at' => $row['deleted_at'],
                'deleted_note' => $row['deleted_note'],
                'sort_id' => $row['sort_id'],
            ]);
            return null;
        }
        // dd($row);

        return new Lead([
            'date' => $row['date'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'country' => $row['country'],
            'date_of_birth' => $row['date_of_birth'],
            'occupation' => $row['occupation'],
            'vc' => $row['vc'],
            'sdm' => $row['sdm'],
            'email' => $row['email'],
            'email_alt_1' => $row['email_alt_1'],
            'email_alt_2' => $row['email_alt_2'],
            'email_alt_3' => $row['email_alt_3'],
            'phone_number' => $row['phone_number'],
            'phone_number_alt_1' => $row['phone_number_alt_1'],
            'phone_number_alt_2' => $row['phone_number_alt_2'],
            'phone_number_alt_3' => $row['phone_number_alt_3'],
            'attachment' => $row['attachment'],
            'private_remark' => $row['private_remark'],
            'remark' => $row['remark'],
            'data_source' => $row['data_source'],
            'appointment_start_at' => $row['appointment_start_at'],
            'appointment_end_at' => $row['appointment_end_at'],
            'contacted_at' => $row['contacted_at'],
            'assignee_read_at' => $row['assignee_read_at'],
            'edited_at' => $row['edited_at'],
            'created_at' => $row['created_at'],
            'appointment_label_id' => $row['appointment_label_id'],
            'assignee_id' => $row['assignee_id'],
            'contact_outcome_id' => $row['contact_outcome_id'],
            'created_by_id' => $row['created_by_id'],
            'stage_id' => $row['stage_id'],
            'give_up_at' => $row['give_up_at'],
            'account_manager' => $row['account_manager'],
            'address' => $row['address'],
            'agents_book' => $row['agents_book'],
            'campaign_product' => $row['campaign_product'],
            'data_code' => $row['data_code'],
            'data_type' => $row['data_type'],
            'deleted_at' => $row['deleted_at'],
            'deleted_note' => $row['deleted_note'],
            'sort_id' => $row['sort_id'],
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|string|max:250', //date_format:Y-m-d H:i:s
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'country' => 'required|string|max:250',
            'date_of_birth' => 'required|string|max:250', //date_format:Y-m-d
            'occupation' => 'required|string|max:250', 
            'vc' => 'required|string|max:250',
            'sdm' => 'required|string|max:250',
            'email' => 'required|email:rfc,dns',
            // 'email_alt_1' => 'required|email:rfc,dns|unique:core_lead|max:254',
            // 'email_alt_2' => 'required|email:rfc,dns|unique:core_lead|max:254',
            // 'email_alt_3' => 'required|email:rfc,dns|unique:core_lead|max:254',
            'phone_number' => 'required|string|unique:core_lead|max:250',
            // 'phone_number_alt_1' => 'required|string|unique:core_lead|max:250',
            // 'phone_number_alt_2' => 'required|string|unique:core_lead|max:250',
            // 'phone_number_alt_3' => 'required|string|unique:core_lead|max:250',
            // 'attachment' => 'required|string|max:250', 
            'private_remark' => 'required|string', 
            'remark' => 'required|string', 
            'data_source' => 'required|string|max:250',
            // 'appointment_start_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'appointment_end_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'contacted_at' => 'required|date_format:Y-m-d H:i:s.uO',
            // 'assignee_read_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'appointment_label_id' => 'nullable|integer',
            'assignee_id' => 'nullable|integer', 
            'contact_outcome_id' => 'nullable|integer', 
            'created_by_id' => 'required|integer', 
            'stage_id' => 'nullable|integer', 
            // 'give_up_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'account_manager' => 'required|string|max:250', 
            'address' => 'required|string', 
            'agents_book' => 'required|string|max:250', 
            'campaign_product' => 'required|string|max:250', 
            'data_code' => 'required|string|max:250',
            'data_type' => 'required|string|max:250',
            // 'deleted_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'deleted_note' => 'required|string|max:250',
            'sort_id' => 'required',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'date' => 'Date Opp&rsquo;d In',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'country' => 'Country',
            'date_of_birth' => 'DOB',
            'occupation' => 'Occupation', 
            'vc' => 'VC',
            'sdm' => 'Sdm', 
            'email' => 'Email',
            'email_alt_1' => 'Additional Email 1', 
            'email_alt_2' => 'Additional Email 2', 
            'email_alt_3' => 'Additional Email 3', 
            'phone_number' => 'Phone Number',
            'phone_number_alt_1' => 'Additional Phone Number 1',
            'phone_number_alt_2' => 'Additional Phone Number 2',
            'phone_number_alt_3' => 'Additional Phone Number 3',
            'attachment' => 'Attachment', 
            'private_remark' => 'Private Remark', 
            'remark' => 'Remark', 
            'data_source' => 'Data Source',
            'appointment_start_at' => 'Appointment Start',
            'appointment_end_at' => 'Appointment End',
            'contacted_at' => 'Last Called',
            'assignee_read_at' => 'Assignee Read At',
            'appointment_label_id' => 'Appointment Label', 
            'assignee_id' => 'Assignee', 
            'contact_outcome_id' => 'Contact Outcome', 
            'created_by_id' => 'Created By', 
            'stage_id' => 'Stage', 
            'give_up_at' => 'Give Up?',
            'account_manager' => 'Account Manager', 
            'address' => 'Address', 
            'agents_book' => 'Agents Book', 
            'campaign_product' => 'Campaign Product', 
            'data_code' => 'Data Code',
            'data_type' => 'Data Type',
            'deleted_at' => 'Delete At',
            'deleted_note' => 'Deleted Note',
            'sort_id' => 'Sort ID',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        
        // $data['contacted_at'] = ($data['contacted_at'] !== '' && $data['contacted_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['contacted_at'])) : $data['contacted_at'];
        // $data['date_of_birth'] = ($data['date_of_birth'] !== '' && $data['date_of_birth'] !== null) ? date('Y-m-d', strtotime($data['date_of_birth'])) : $data['date_of_birth'];
        // $data['date'] = ($data['date'] !== '' && $data['date'] !== null) ? date('Y-m-d H:i:s', strtotime($data['date'])) : $data['date'];
        // $data['appointment_start_at'] = ($data['appointment_start_at'] !== '' && $data['appointment_start_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['appointment_start_at'])) : $data['appointment_start_at'];
        // $data['appointment_end_at'] = ($data['appointment_end_at'] !== '' && $data['appointment_end_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['appointment_end_at'])) : $data['appointment_end_at'];
        // $data['assignee_read_at'] = ($data['assignee_read_at'] !== '' && $data['assignee_read_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['assignee_read_at'])) : $data['assignee_read_at'];
        // $data['give_up_at'] = ($data['give_up_at'] !== '' && $data['give_up_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['give_up_at'])) : $data['give_up_at'];
        // $data['deleted_at'] = ($data['deleted_at'] !== '' && $data['deleted_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['deleted_at'])) : $data['deleted_at'];
        
        // dd($data);
        // dd($data['give_up_at']);
        // if(isset($data['assignee']) && gettype($data['assignee']) !== 'string') {
        //     $data['assignee'] = strval($data['assignee']);
        // }
        
        return $data;
    }

}
