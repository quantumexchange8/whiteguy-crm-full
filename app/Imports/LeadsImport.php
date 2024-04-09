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

class LeadsImport implements ToModel, WithValidation, SkipsOnFailure //, WithHeadingRow
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
        if (Lead::where('email', $row['8'])->orWhere('phone_number', $row['12'])->exists()) {
            // If a duplicate is found, insert the data into the duplicated_leads table

            // dd($this->convertExcelTimestampToLocalTimezone($row[0]), $this->convertExcelTimestampToLocalTimezone($row[20]));
            // dd([
            //     'date' => $row[0] ? $this->convertExcelTimestampToLocalTimezone($row[0]) : '',
            //     'first_name' => $row[1] ?? '',
            //     'last_name' => $row[2] ?? '',
            //     'country' => $row[3] ?? '',
            //     'date_of_birth' => $row[4] ?? '',
            //     'occupation' => $row[5] ?? '',
            //     'vc' => $row[6] ?? '',
            //     'sdm' => $row[7] ?? '',
            //     'email' => $row[8] ?? '',
            //     'email_alt_1' => $row[9] ?? '',
            //     'email_alt_2' => $row[10] ?? '',
            //     'email_alt_3' => $row[11] ?? '',
            //     'phone_number' => $row[12] ?? '',
            //     'phone_number_alt_1' => $row[13] ?? '',
            //     'phone_number_alt_2' => $row[14] ?? '',
            //     'phone_number_alt_3' => $row[15] ?? '',
            //     'attachment' => $row[16] ?? '',
            //     'private_remark' => $row[17] ?? '',
            //     'remark' => $row[18] ?? '',
            //     'data_source' => $row[19] ?? '',
            //     'appointment_start_at' => $row[20] ? $this->convertExcelTimestampToLocalTimezone($row[20]) : '',
            //     'appointment_end_at' => $row[21] ? $this->convertExcelTimestampToLocalTimezone($row[21]) : '',
            //     'contacted_at' => $row[22] ? $this->convertExcelTimestampToLocalTimezone($row[22]) : '',
            //     'assignee_read_at' => $row[23] ? $this->convertExcelTimestampToLocalTimezone($row[23]) : '',
            //     'edited_at' => (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
            //     'created_at' => (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
            //     'appointment_label_id' => $row[26] ? $this->getAppointmentLabelId($row[26]) : '',
            //     'assignee_id' => $row[27] ? $this->getAssigneeId($row[27]) : '',
            //     'contact_outcome_id' => $row[28] ? $this->getContactOutcomeId($row[28]) : '',
            //     'created_by_id' => $row[29] ? $this->getCreatedById($row[29]) : '',
            //     'stage_id' => $row[30] ? $this->getStageId($row[30]) : '',
            //     'give_up_at' => $row[31] ? $this->convertExcelTimestampToLocalTimezone($row[31]) : '',
            //     'account_manager' => $row[32] ?? '',
            //     'address' => $row[33] ?? '',
            //     'agents_book' => $row[34] ?? '',
            //     'campaign_product' => $row[35] ?? '',
            //     'data_code' => $row[36] ?? '',
            //     'data_type' => $row[37] ?? '',
            //     'deleted_at' =>$row[38] ? $this->convertExcelTimestampToLocalTimezone($row[38]) : '',
            //     'deleted_note' => $row[39] ?? '',
            //     'sort_id' => $this->generateLeadSortUuid(),
            // ]);
            LeadDuplicated::create([
                'date' => $row[0] ? $this->convertExcelTimestampToLocalTimezone($row[0]) : '',
                'first_name' => $row[1] ?? '',
                'last_name' => $row[2] ?? '',
                'country' => $row[3] ?? '',
                'date_of_birth' => $row[4] ?? '',
                'occupation' => $row[5] ?? '',
                'vc' => $row[6] ?? '',
                'sdm' => $row[7] ?? '',
                'email' => $row[8] ?? '',
                'email_alt_1' => $row[9] ?? '',
                'email_alt_2' => $row[10] ?? '',
                'email_alt_3' => $row[11] ?? '',
                'phone_number' => $row[12] ?? '',
                'phone_number_alt_1' => $row[13] ?? '',
                'phone_number_alt_2' => $row[14] ?? '',
                'phone_number_alt_3' => $row[15] ?? '',
                'attachment' => $row[16] ?? '',
                'private_remark' => $row[17] ?? '',
                'remark' => $row[18] ?? '',
                'data_source' => $row[19] ?? '',
                'appointment_start_at' => $row[20] ? $this->convertExcelTimestampToLocalTimezone($row[20]) : '',
                'appointment_end_at' => $row[21] ? $this->convertExcelTimestampToLocalTimezone($row[21]) : '',
                'contacted_at' => $row[22] ? $this->convertExcelTimestampToLocalTimezone($row[22]) : '',
                'assignee_read_at' => $row[23] ? $this->convertExcelTimestampToLocalTimezone($row[23]) : '',
                'edited_at' => (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
                'created_at' => (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
                'appointment_label_id' => $row[26] ? $this->getAppointmentLabelId($row[26]) : '',
                'assignee_id' => $row[27] ? $this->getAssigneeId($row[27]) : '',
                'contact_outcome_id' => $row[28] ? $this->getContactOutcomeId($row[28]) : '',
                'created_by_id' => $row[29] ? $this->getCreatedById($row[29]) : '',
                'stage_id' => $row[30] ? $this->getStageId($row[30]) : '',
                'give_up_at' => $row[31] ? $this->convertExcelTimestampToLocalTimezone($row[31]) : '',
                'account_manager' => $row[32] ?? '',
                'address' => $row[33] ?? '',
                'agents_book' => $row[34] ?? '',
                'campaign_product' => $row[35] ?? '',
                'data_code' => $row[36] ?? '',
                'data_type' => $row[37] ?? '',
                'deleted_at' =>$row[38] ? $this->convertExcelTimestampToLocalTimezone($row[38]) : '',
                'deleted_note' => $row[39] ?? '',
                'sort_id' => $this->generateLeadSortUuid(),
            ]);
            return null;
        }

        $string ='why la bro';
        $subTxt = substr($string, 0, strpos($string, 'bro'));

        // dd($row);

        return new Lead([
            'date' => $row[0] ? $this->convertExcelTimestampToLocalTimezone($row[0]) : '',
            'first_name' => $row[1] ?? '',
            'last_name' => $row[2] ?? '',
            'country' => $row[3] ?? '',
            'date_of_birth' => $row[4] ?? '',
            'occupation' => $row[5] ?? '',
            'vc' => $row[6] ?? '',
            'sdm' => $row[7] ?? '',
            'email' => $row[8] ?? '',
            'email_alt_1' => $row[9] ?? '',
            'email_alt_2' => $row[10] ?? '',
            'email_alt_3' => $row[11] ?? '',
            'phone_number' => $row[12] ?? '',
            'phone_number_alt_1' => $row[13] ?? '',
            'phone_number_alt_2' => $row[14] ?? '',
            'phone_number_alt_3' => $row[15] ?? '',
            'attachment' => $row[16] ?? '',
            'private_remark' => $row[17] ?? '',
            'remark' => $row[18] ?? '',
            'data_source' => $row[19] ?? '',
            'appointment_start_at' => $this->convertExcelTimestampToLocalTimezone($row[20]),
            'appointment_end_at' => $this->convertExcelTimestampToLocalTimezone($row[21]),
            'contacted_at' => $this->convertExcelTimestampToLocalTimezone($row[22]),
            'assignee_read_at' => $this->convertExcelTimestampToLocalTimezone($row[23]),
            'edited_at' => (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
            'created_at' => (new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d H:i:s.u') . "+08",
            'appointment_label_id' => $row[26] ? $this->getAppointmentLabelId($row[26]) : '',
            'assignee_id' => $row[27] ? $this->getAssigneeId($row[27]) : '',
            'contact_outcome_id' => $row[28] ? $this->getContactOutcomeId($row[28]) : '',
            'created_by_id' => $row[29] ? $this->getCreatedById($row[29]) : '',
            'stage_id' => $row[30] ? $this->getStageId($row[30]) : '',
            'give_up_at' => $this->convertExcelTimestampToLocalTimezone($row[31]),
            'account_manager' => $row[32] ?? '',
            'address' => $row[33] ?? '',
            'agents_book' => $row[34] ?? '',
            'campaign_product' => $row[35] ?? '',
            'data_code' => $row[36] ?? '',
            'data_type' => $row[37] ?? '',
            'deleted_at' =>$this->convertExcelTimestampToLocalTimezone($row[38]),
            'deleted_note' => $row[39] ?? '',
            'sort_id' => $this->generateLeadSortUuid(),
        ]);
    }

    // public function headingRow(): int
    // {
    //     return 1;
    // }

    public function rules(): array
    {
        return [
            // 'date' => 'required|string|max:250', //date_format:Y-m-d H:i:s
            // 'first_name' => 'required|string|max:250',
            // 'last_name' => 'required|string|max:250',
            // 'country' => 'required|string|max:250',
            // 'date_of_birth' => 'required|string|max:250', //date_format:Y-m-d
            // 'occupation' => 'required|string|max:250', 
            // 'vc' => 'required|string|max:250',
            // 'sdm' => 'required|string|max:250',
            // 'email' => 'required|email:rfc,dns',
            // // 'email_alt_1' => 'required|email:rfc,dns|unique:core_lead|max:254',
            // // 'email_alt_2' => 'required|email:rfc,dns|unique:core_lead|max:254',
            // // 'email_alt_3' => 'required|email:rfc,dns|unique:core_lead|max:254',
            // 'phone_number' => 'required|string|unique:core_lead|max:250',
            // // 'phone_number_alt_1' => 'required|string|unique:core_lead|max:250',
            // // 'phone_number_alt_2' => 'required|string|unique:core_lead|max:250',
            // // 'phone_number_alt_3' => 'required|string|unique:core_lead|max:250',
            // // 'attachment' => 'required|string|max:250', 
            // 'private_remark' => 'required|string', 
            // 'remark' => 'required|string', 
            // 'data_source' => 'required|string|max:250',
            // // 'appointment_start_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // // 'appointment_end_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'contacted_at' => 'required|date_format:Y-m-d H:i:s.uO',
            // // 'assignee_read_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            // 'created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            // 'appointment_label_id' => 'nullable|integer',
            // 'assignee_id' => 'nullable|integer', 
            // 'contact_outcome_id' => 'nullable|integer', 
            // 'created_by_id' => 'required|integer', 
            // 'stage_id' => 'nullable|integer', 
            // // 'give_up_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'account_manager' => 'required|string|max:250', 
            // 'address' => 'required|string', 
            // 'agents_book' => 'required|string|max:250', 
            // 'campaign_product' => 'required|string|max:250', 
            // 'data_code' => 'required|string|max:250',
            // 'data_type' => 'required|string|max:250',
            // // 'deleted_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'deleted_note' => 'required|string|max:250',
            // 'sort_id' => 'required',
        ];
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

    /**
     * Convert Excel timestamp to local time with a specific timezone offset.
     *
     * @param float $excelTimestamp The Excel timestamp.
     * @return string Formatted date time with the target timezone offset.
     */
    function convertExcelTimestampToLocalTimezone($excelTimestamp): string 
    {
        if (gettype($excelTimestamp) === 'string' || str_contains($excelTimestamp, '+08')) {
            return $excelTimestamp;
        }

        if ($excelTimestamp === null || $excelTimestamp === '') {
            return null;
        }

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
        $newDate = (new DateTime('@' . $timestamp))->format('Y-m-d H:i:s.uO') . "+08";

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
