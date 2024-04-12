<?php

namespace App\Imports;

use App\Models\LeadAppointmentLabel;
use App\Models\LeadContactOutcome;
use App\Models\LeadDuplicated;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
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

class LeadsImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow
{
    use Importable, SkipsFailures;

    // public function headings(): array
    // {
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
    // }

    public function model(array $row)
    {
        // Check for duplicates based on email and phone number
        if (Lead::where('first_name', $row['first_name'])
                    ->where('last_name', $row['last_name'])
                    ->where('email', $row['email'])
                    ->where('phone_number', $row['phone_number'])
                    ->exists()
            ) 
        {
            // If a duplicate is found, insert the data into the duplicated_leads table
            return new LeadDuplicated([
                'date' => $row['date'] ? $this->convertExcelTimestampToLocalTimezone($row['date']) : '',
                'first_name' => $row['first_name'] ?? '',
                'last_name' => $row['last_name'] ?? '',
                'country' => $row['country'] ?? '',
                'date_of_birth' => $row['date_of_birth'] ?? '',
                'occupation' => $row['occupation'] ?? '',
                'vc' => $row['vc'] ?? '',
                'sdm' => $row['sdm'] ?? '',
                'email' => $row['email'] ?? '',
                'email_alt_1' => $row['email_alt_1'] ?? '',
                'email_alt_2' => $row['email_alt_2'] ?? '',
                'email_alt_3' => $row['email_alt_3'] ?? '',
                'phone_number' => $row['phone_number'] ?? '',
                'phone_number_alt_1' => $row['phone_number_alt_1'] ?? '',
                'phone_number_alt_2' => $row['phone_number_alt_2'] ?? '',
                'phone_number_alt_3' => $row['phone_number_alt_3'] ?? '',
                'attachment' => $row['attachment'] ?? '',
                'private_remark' => $row['private_remark'] ?? '',
                'remark' => $row['remark'] ?? '',
                'data_source' => $row['data_source'] ?? '',
                'appointment_start_at' => $row['appointment_start_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['appointment_start_at'])) : null,
                'appointment_end_at' => $row['appointment_end_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['appointment_end_at'])) : null,
                'contacted_at' => $row['contacted_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['contacted_at'])) : null,
                'assignee_read_at' => $row['assignee_read_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['assignee_read_at'])) : null,
                'edited_at' => $row['edited_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['edited_at'])) : (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
                'created_at' => $row['created_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['created_at'])) : (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
                'appointment_label_id' => $row['appointment_label_id'] ? $this->getAppointmentLabelId($row['appointment_label_id']) : null,
                'assignee_id' => $row['assignee_id'] ? $this->getAssigneeId($row['assignee_id']) : null,
                'contact_outcome_id' => $row['contact_outcome_id'] ? $this->getContactOutcomeId($row['contact_outcome_id']) : null,
                'created_by_id' => $row['created_by_id'] ? $this->getCreatedById($row['created_by_id']) : '',
                'stage_id' => $row['stage_id'] ? $this->getStageId($row['stage_id']) : null,
                'give_up_at' => $row['give_up_at'] ? $this->convertExcelTimestampToLocalTimezone($row['give_up_at']) : null,
                'account_manager' => $row['account_manager'] ?? '',
                'address' => $row['address'] ?? '',
                'agents_book' => $row['agents_book'] ?? '',
                'campaign_product' => $row['campaign_product'] ?? '',
                'data_code' => $row['data_code'] ?? '',
                'data_type' => $row['data_type'] ?? '',
                'deleted_at' =>$row['deleted_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['deleted_at'])) : null,
                'deleted_note' => (string)$row['deleted_note'] ?? '',
                'sort_id' => $row['sort_id'] ? $row['sort_id'] : $this->generateLeadSortUuid(),
            ]);
        }

        // $string ='why la bro';
        // $subTxt = substr($string, 0, strpos($string, 'bro'));

        return new Lead([
            'date' => $row['date'] ? $this->convertExcelTimestampToLocalTimezone($row['date']) : '',
            'first_name' => $row['first_name'] ?? '',
            'last_name' => $row['last_name'] ?? '',
            'country' => $row['country'] ?? '',
            'date_of_birth' => $row['date_of_birth'] ?? '',
            'occupation' => $row['occupation'] ?? '',
            'vc' => $row['vc'] ?? '',
            'sdm' => $row['sdm'] ?? '',
            'email' => $row['email'] ?? '',
            'email_alt_1' => $row['email_alt_1'] ?? '',
            'email_alt_2' => $row['email_alt_2'] ?? '',
            'email_alt_3' => $row['email_alt_3'] ?? '',
            'phone_number' => $row['phone_number'] ?? '',
            'phone_number_alt_1' => $row['phone_number_alt_1'] ?? '',
            'phone_number_alt_2' => $row['phone_number_alt_2'] ?? '',
            'phone_number_alt_3' => $row['phone_number_alt_3'] ?? '',
            'attachment' => $row['attachment'] ?? '',
            'private_remark' => $row['private_remark'] ?? '',
            'remark' => $row['remark'] ?? '',
            'data_source' => $row['data_source'] ?? '',
            'appointment_start_at' => $row['appointment_start_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['appointment_start_at'])) : null,
            'appointment_end_at' => $row['appointment_end_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['appointment_end_at'])) : null,
            'contacted_at' => $row['contacted_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['contacted_at'])) : null,
            'assignee_read_at' => $row['assignee_read_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['assignee_read_at'])) : null,
            'edited_at' => $row['edited_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['edited_at'])) : (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
            'created_at' => $row['created_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['created_at'])) : (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
            'appointment_label_id' => $row['appointment_label_id'] ? $this->getAppointmentLabelId($row['appointment_label_id']) : null,
            'assignee_id' => $row['assignee_id'] ? $this->getAssigneeId($row['assignee_id']) : null,
            'contact_outcome_id' => $row['contact_outcome_id'] ? $this->getContactOutcomeId($row['contact_outcome_id']) : null,
            'created_by_id' => $row['created_by_id'] ? $this->getCreatedById($row['created_by_id']) : '',
            'stage_id' => $row['stage_id'] ? $this->getStageId($row['stage_id']) : null,
            'give_up_at' => $row['give_up_at'] ? $this->convertExcelTimestampToLocalTimezone($row['give_up_at']) : null,
            'account_manager' => $row['account_manager'] ?? '',
            'address' => $row['address'] ?? '',
            'agents_book' => $row['agents_book'] ?? '',
            'campaign_product' => $row['campaign_product'] ?? '',
            'data_code' => $row['data_code'] ?? '',
            'data_type' => $row['data_type'] ?? '',
            'deleted_at' =>$row['deleted_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $this->convertExcelTimestampToLocalTimezone($row['deleted_at'])) : null,
            'deleted_note' => (string)$row['deleted_note'] ?? '',
            'sort_id' => $row['sort_id'] ? $row['sort_id'] : $this->generateLeadSortUuid(),
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        $rules = [
            'date' => 'required|string|max:250', //date_format:Y-m-d H:i:s
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'country' => 'required|string|max:250',
            'date_of_birth' => 'required|string|max:250', //date_format:Y-m-d
            'occupation' => 'required|string|max:250', 
            'vc' => 'required|string|max:250',
            'sdm' => 'required|string|max:250',
            'email' => 'required|email:rfc,dns',
            // 'email_alt_1' => 'required|email:rfc,dns|max:254',
            // 'email_alt_2' => 'required|email:rfc,dns|max:254',
            // 'email_alt_3' => 'required|email:rfc,dns|max:254',
            'phone_number' => 'required|string|max:250',
            // 'phone_number_alt_1' => 'required|string|max:250',
            // 'phone_number_alt_2' => 'required|string|max:250',
            // 'phone_number_alt_3' => 'required|string|max:250',
            // 'attachment' => 'required|string|max:250', 
            'private_remark' => 'required|string', 
            'remark' => 'required|string', 
            'data_source' => 'required|string|max:250',
            'appointment_start_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'appointment_end_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'contacted_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'assignee_read_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'appointment_label_id' => 'nullable',
            'assignee_id' => 'nullable', 
            'contact_outcome_id' => 'nullable', 
            'created_by_id' => 'required', 
            'stage_id' => 'nullable', 
            'give_up_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'account_manager' => 'required|string|max:250', 
            'address' => 'required|string', 
            'agents_book' => 'required|string|max:250', 
            'campaign_product' => 'required|string|max:250', 
            'data_code' => 'required|string|max:250',
            'data_type' => 'required|string|max:250',
            'deleted_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'deleted_note' => [
                'required_with:deleted_at',
                'max:250',
            ],
            'sort_id' => 'nullable',
        ];

        return $rules;
    }

    public function customValidationAttributes()
    {
        return [
            'date' => "Date Opp'd In",
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
            'contacted_at' => 'Contacted At',
            'assignee_read_at' => 'Assignee Read At',
            'appointment_label' => 'Appointment Label', 
            'assignee' => 'Assignee', 
            'contact_outcome' => 'Contact Outcome', 
            'created_by' => 'Created By', 
            'stage' => 'Stage', 
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
        $data['date'] = (string)$data['date'];
        // $data['deleted_note'] = $data['deleted_note'] ?? '';

        // Format nullable datetime fields
        $nullableDateTimeFieldsArr = [
            'appointment_start_at',
            'appointment_end_at',
            'contacted_at',
            'assignee_read_at',
            'give_up_at',
            'deleted_at',
        ];
    
        foreach ($nullableDateTimeFieldsArr as $field) {
            $data[$field] = $data[$field]
                                ? $this->convertExcelTimestampToLocalTimezone($data[$field])
                                : null;
        }

        // Format required datetime fields
        $requiredDateTimeFieldsArr = [
            'edited_at',
            'created_at',
        ];
    
        foreach ($requiredDateTimeFieldsArr as $field) {
            $data[$field] = $data[$field]
                                ? $this->convertExcelTimestampToLocalTimezone($data[$field])
                                : '';
        }

        // dd($data);
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

    /**
     * Convert Excel timestamp to local time with a specific timezone offset.
     *
     * @param float $excelTimestamp The Excel timestamp.
     * @return string Formatted date time with the target timezone offset.
     */
    function convertExcelTimestampToLocalTimezone($excelTimestamp): string 
    {
        
        if (gettype($excelTimestamp) === 'string' && str_contains($excelTimestamp, '+08')) {
            
            return  (new DateTime($excelTimestamp))->format('Y-m-d H:i:s.uO');
        }
        
        if ($excelTimestamp === null || $excelTimestamp === '') {
            return null;
        }
        
        $excelTimestamp = (float)$excelTimestamp;
        
        // Set timezone
        $timezone = new DateTimeZone(auth()->user()->timezone);
        $baseTimezone = new DateTimeZone('UTC');
        $convertTimezone = new DateTimeZone('Asia/Kuala_Lumpur');

        // Get the offset in seconds
        $offsetSeconds = $timezone->getOffset(new DateTime());
        $baseOffsetSeconds = $baseTimezone->getOffset(new DateTime());
        $convertOffsetSeconds = $convertTimezone->getOffset(new DateTime());

        // Calculate differences in offset between auth user timezone against UTC timezone then converting to the target timezone offset
        $offsetDifferences = ($baseOffsetSeconds - $offsetSeconds) + $convertOffsetSeconds;

        // Setting calculated timestamp
        $timestamp = ($excelTimestamp - 25569) * 86400;
        $timestamp += $offsetDifferences;

        // Set and format the converted date time with timezone
        $newDate = (new DateTime('@' . $timestamp))->format('Y-m-d H:i:s.u') . "+08";

        return $newDate;
    }

    public function generateLeadSortUuid()
    {
        do {
            $newLeadUuid = Str::uuid();
        } while ($this->checkExistingLeadSortUuid($newLeadUuid));
        
        return (string) $newLeadUuid;
    }

    public function checkExistingLeadSortUuid($string)
    {
        $existingLeadUuid = Lead::where('sort_id', $string)->get();

        if (count($existingLeadUuid) > 0) {
            return true;
        }
        
        return false;
    }

    public function getAppointmentLabelId($string)
    {
        $appointmentLabelId = LeadAppointmentLabel::select('id')
                                                    ->where('title', $string)
                                                    ->get();
    
        return $appointmentLabelId[0]->id;
    }

    public function getAssigneeId($string)
    {
        $assigneeId = User::select('id')
                            ->where('username', substr($string, 0, strpos($string, ' (')))
                            ->limit(1)
                            ->get();
        
        return $assigneeId[0]->id;
    }

    public function getContactOutcomeId($string)
    {
        $contactOutcomeId = LeadContactOutcome::select('id')
                                                ->where('title', $string)
                                                ->get();
    
        return $contactOutcomeId[0]->id;
    }

    public function getCreatedById($string)
    {
        $createdById = User::select('id')
                            ->where('username', substr($string, 0, strpos($string, ' (')))
                            ->limit(1)
                            ->get();
        
        return $createdById[0]->id;
    }

    public function getStageId($string)
    {
        $stageId = LeadStage::select('id')
                                ->where('title', $string)
                                ->get();
    
        return $stageId[0]->id;
    }

}
