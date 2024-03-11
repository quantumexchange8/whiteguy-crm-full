<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings
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
            'Site',
            'Username',
            'Password',
            'Account Id',
            'First Name',
            'Last Name',
            'Full Legal Name',
            'Email',
            'Phone Number',
            'Address',
            'Country of Citizenship',
            'Account Holder',
            'Account Type',
            'Customer Type',
            'Account Manager',
            'Lead Status',
            'Client Stage',
            'Rank',
            'Remark',
            'Previous Broker Name',
            'Kyc Status',
            'Is Active',
            'Has Crm Access',
            'Has Leads Access',
            'Is_Staff',
            'Is Superuser',
            'Last Login',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function query()
    {
        return User::query()->whereIn('id', $this->users);
    }
}
