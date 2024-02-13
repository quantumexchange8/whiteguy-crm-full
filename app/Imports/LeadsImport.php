<?php

namespace App\Imports;

use App\Models\DuplicatedLead;
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

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        // Check for duplicates based on email and phone number
        if (Lead::where('email', $row['email'])->orWhere('phone_number', $row['phone_number'])->exists()) {
            // If a duplicate is found, insert the data into the duplicated_leads table
            
            DuplicatedLead::create([
                'first_name' => $row['first_name'], 
                'last_name' => $row['last_name'], 
                'country' => $row['country'], 
                'address' => $row['address'],
                'date_oppd_in' => $row['date_oppd_in'], 
                'campaign_product' => $row['campaign_product'],
                'sdm' => $row['sdm'],
                'date_of_birth' => $row['date_of_birth'],
                'occupation' => $row['occupation'],
                'agents_book' => $row['agents_book'],
                'account_manager' => $row['account_manager'],
                'vc' => $row['vc'], 
                'data_type' => $row['data_type'],
                'data_source' => $row['data_source'],
                'data_code' => $row['data_code'],
                'email' => $row['email'], 
                'email_alt_1' => $row['email_alt_1'],
                'email_alt_2' => $row['email_alt_2'],
                'email_alt_3' => $row['email_alt_3'],
                'phone_number' => $row['phone_number'],
                'phone_number_alt_1' => $row['phone_number_alt_1'],
                'phone_number_alt_2' => $row['phone_number_alt_2'],
                'phone_number_alt_3' => $row['phone_number_alt_3'],
                'private_remark' => $row['private_remark'],
                'remark' => $row['remark'],
                'appointment_start_at' => $row['appointment_start_at'],
                'appointment_end_at' => $row['appointment_end_at'],
                'last_called' => $row['last_called'], 
                'assignee_read_at' => $row['assignee_read_at'],
                'give_up_at' => $row['give_up_at'], 
                'appointment_label' => $row['appointment_label'],
                'contact_outcome' => $row['contact_outcome'],
                'stage' => $row['stage'],
                'assignee' => $row['assignee'], 
                'created_by' => $row['created_by'],
                'delete_at' => $row['delete_at'],
            ]);
            return null;
        }

        return new Lead([
            'first_name' => $row['first_name'], 
            'last_name' => $row['last_name'], 
            'country' => $row['country'], 
            'address' => $row['address'],
            'date_oppd_in' => $row['date_oppd_in'], 
            'campaign_product' => $row['campaign_product'],
            'sdm' => $row['sdm'],
            'date_of_birth' => $row['date_of_birth'],
            'occupation' => $row['occupation'],
            'agents_book' => $row['agents_book'],
            'account_manager' => $row['account_manager'],
            'vc' => $row['vc'], 
            'data_type' => $row['data_type'],
            'data_source' => $row['data_source'],
            'data_code' => $row['data_code'],
            'email' => $row['email'], 
            'email_alt_1' => $row['email_alt_1'],
            'email_alt_2' => $row['email_alt_2'],
            'email_alt_3' => $row['email_alt_3'],
            'phone_number' => $row['phone_number'],
            'phone_number_alt_1' => $row['phone_number_alt_1'],
            'phone_number_alt_2' => $row['phone_number_alt_2'],
            'phone_number_alt_3' => $row['phone_number_alt_3'],
            'private_remark' => $row['private_remark'],
            'remark' => $row['remark'],
            'appointment_start_at' => $row['appointment_start_at'],
            'appointment_end_at' => $row['appointment_end_at'],
            'last_called' => $row['last_called'], 
            'assignee_read_at' => $row['assignee_read_at'],
            'give_up_at' => $row['give_up_at'], 
            'appointment_label' => $row['appointment_label'],
            'contact_outcome' => $row['contact_outcome'],
            'stage' => $row['stage'],
            'assignee' => $row['assignee'], 
            'created_by' => $row['created_by'],
            'delete_at' => $row['delete_at'],
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:150',
            'last_name' => 'string|nullable|max:150',
            'country' => 'string|nullable|max:100',
            'address' => 'string|nullable|max:200', 
            'date_oppd_in' => 'date_format:Y-m-d H:i:s|nullable',
            'campaign_product' => 'string|nullable|max:100', 
            'sdm' => 'string|nullable|max:100', 
            'date_of_birth' => 'date_format:Y-m-d|nullable',
            'occupation' => 'string|nullable|max:150', 
            'agents_book' => 'string|nullable|max:150', 
            'account_manager' => 'string|nullable', 
            'vc' => 'required|string|max:100',
            'data_type' => 'string|nullable|max:100',
            'data_source' => 'string|nullable|max:100',
            'data_code' => 'string|nullable|max:100',
            'email' => 'required|email:rfc,dns',
            'email_alt_1' => 'nullable|email:rfc,dns',
            'email_alt_2' => 'nullable|email:rfc,dns',
            'email_alt_3' => 'nullable|email:rfc,dns',
            'phone_number' => 'required|integer',
            'phone_number_alt_1' => 'nullable|integer',
            'phone_number_alt_2' => 'nullable|integer',
            'phone_number_alt_3' => 'nullable|integer',
            'private_remark' => 'string|nullable|max:1000', 
            'remark' => 'string|nullable|max:1000', 
            'appointment_start_at' => 'date_format:Y-m-d H:i:s|nullable',
            'appointment_end_at' => 'date_format:Y-m-d H:i:s|nullable',
            'last_called' => 'required|date_format:Y-m-d H:i:s',
            'assignee_read_at' => 'date_format:Y-m-d H:i:s|nullable',
            'give_up_at' => 'date_format:Y-m-d H:i:s|nullable',
            'appointment_label' => 'string|nullable', 
            'contact_outcome' => 'string|nullable', 
            'stage' => 'string|nullable', 
            'assignee' => 'required|string', 
            'created_by' => 'string|nullable', 
            'delete_at' => 'date_format:Y-m-d H:i:s|nullable',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'country' => 'Country',
            'address' => 'Address', 
            'date_oppd_in' => 'Date Opp&rsquo;d In',
            'campaign_product' => 'Campaign Product', 
            'sdm' => 'Sdm', 
            'date_of_birth' => 'DOB',
            'occupation' => 'Occupation', 
            'agents_book' => 'Agents Book', 
            'account_manager' => 'Account Manager', 
            'vc' => 'VC',
            'data_type' => 'Data Type',
            'data_source' => 'Data Source',
            'data_code' => 'Data Code',
            'email' => 'Email',
            'email_alt_1' => 'Additional Email 1', 
            'email_alt_2' => 'Additional Email 2', 
            'email_alt_3' => 'Additional Email 3', 
            'phone_number' => 'Phone Number',
            'phone_number_alt_1' => 'Additional Phone Number 1',
            'phone_number_alt_2' => 'Additional Phone Number 2',
            'phone_number_alt_3' => 'Additional Phone Number 3',
            'private_remark' => 'Private Remark', 
            'remark' => 'Remark', 
            'appointment_start_at' => 'Appointment Start',
            'appointment_end_at' => 'Appointment End',
            'last_called' => 'Last Called',
            'assignee_read_at' => 'Assignee Read At',
            'give_up_at' => 'Give Up?',
            'appointment_label' => 'Appointment Label', 
            'contact_outcome' => 'Contact Outcome', 
            'stage' => 'Stage', 
            'assignee' => 'Assignee', 
            'created_by' => 'Created By', 
            'delete_at' => 'Delete At',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $data['last_called'] = ($data['last_called'] !== '' && $data['last_called'] !== null) ? date('Y-m-d H:i:s', strtotime($data['last_called'])) : $data['last_called'];
        $data['date_of_birth'] = ($data['date_of_birth'] !== '' && $data['date_of_birth'] !== null) ? date('Y-m-d', strtotime($data['date_of_birth'])) : $data['date_of_birth'];
        $data['date_oppd_in'] = ($data['date_oppd_in'] !== '' && $data['date_oppd_in'] !== null) ? date('Y-m-d H:i:s', strtotime($data['date_oppd_in'])) : $data['date_oppd_in'];
        $data['appointment_start_at'] = ($data['appointment_start_at'] !== '' && $data['appointment_start_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['appointment_start_at'])) : $data['appointment_start_at'];
        $data['appointment_end_at'] = ($data['appointment_end_at'] !== '' && $data['appointment_end_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['appointment_end_at'])) : $data['appointment_end_at'];
        $data['assignee_read_at'] = ($data['assignee_read_at'] !== '' && $data['assignee_read_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['assignee_read_at'])) : $data['assignee_read_at'];
        $data['give_up_at'] = ($data['give_up_at'] !== '' && $data['give_up_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['give_up_at'])) : $data['give_up_at'];
        $data['delete_at'] = ($data['delete_at'] !== '' && $data['delete_at'] !== null) ? date('Y-m-d H:i:s', strtotime($data['delete_at'])) : $data['delete_at'];

        // dd($data['give_up_at']);
        if(isset($data['assignee']) && gettype($data['assignee']) !== 'string') {
            $data['assignee'] = strval($data['assignee']);
        }
        
        return $data;
    }

}
