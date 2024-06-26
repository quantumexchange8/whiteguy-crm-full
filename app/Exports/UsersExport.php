<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithDefaultStyles
{
    use Exportable;

    protected $users;
    
    public function __construct(array $users)
    {
        $this->users = $users;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'Last Login',
            'Is Superuser',
            'First Name',
            'Last Name',
            'Is Staff',
            'Is Active',
            'Date Joined',
            'Username',
            'Full Legal Name',
            'Email',
            'Phone Number',
            'Is Email Verified',
            'Timezone',
            'Country',
            'Address',
            'Account Type',
            'Account Holder',
            'Customer Type',
            'Rank',
            'Remark',
            'Wallet Balance',
            'Account Manager',
            'Site',
            'Has Crm Access',
            'Lead Status',
            'Client Stage',
            'Has Leads Access',
            'Kyc Status',
            'Account Id',
            'Previous Broker Name',
            'Edited At',
            'Created At',
        ];
    }

    /**
    * @param User $user
    */
    public function map($user): array
    {
        $acc_types = [ "Individual", "Joint", "Trust", "Corporate" ];
        $ranks =[ "Normal", "VIP" ];
        $client_stages = [ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ];
        $kyc_statuses = [ "Not started", "Pending documents", "In progress", "Rejected", "Approved" ];

        return [
            $user->id,
            $user->last_login,
            (!$user->is_superuser) ? 'FALSE' : $user->is_superuser,
            $user->first_name,
            $user->last_name,
            (!$user->is_staff) ? 'FALSE' : $user->is_staff,
            (!$user->is_active) ? 'FALSE' : $user->is_active,
            $user->date_joined,
            $user->username,
            $user->full_name,
            $user->email,
            $user->phone_number,
            (!$user->is_email_verified) ? 'FALSE' : $user->is_email_verified,
            $user->timezone,
            $user->country,
            $user->address,
            $user->account_type ? $acc_types[$user->account_type - 1] : '',
            $user->account_holder,
            $user->customer_type,
            $user->rank ? $ranks[$user->rank - 1] : '',
            $user->remark,
            number_format(((float)$user->wallet_balance), 2, '.', ''),
            $user->account_manager_id ? $user->accountManager->username . ' (' . $user->accountManager->site->name . ')' : 'No Account Manager Set',
            $user->site_id ? $user->site->domain . ' (' . $user->site->name . ')' : 'No Site Set',
            (!$user->has_crm_access) ? 'FALSE' : $user->has_crm_access,
            $user->lead_status,
            $user->client_stage ? $client_stages[$user->client_stage - 1] : '',
            (!$user->has_leads_access) ? 'FALSE' : $user->has_leads_access,
            $user->kyc_status ? $kyc_statuses[$user->kyc_status - 1] : '',
            $user->account_id,
            $user->previous_broker_name,
            $user->edited_at,
            $user->created_at,
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        // Or return the styles array
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_JUSTIFY,
            ],
        ];
    }

    public function query()
    {
        return User::query()
                        ->with([
                                    'site',
                                    'accountManager:id,username,site_id',
                                    'accountManager.site:id,name',
                                ])
                        ->orderByDesc('id')
                        ->whereIn('id', $this->users);
    }
}
