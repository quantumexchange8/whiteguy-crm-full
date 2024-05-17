<?php

namespace App\Exports;

use App\Models\AccountManagerProfile;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AccountManagerProfileExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $accountManagerProfiles;
    
    public function __construct(array $accountManagerProfiles)
    {
        $this->accountManagerProfiles = $accountManagerProfiles;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'File', 
            'User',
            'Edited At', 
            'Created At',
        ];
    }

    /**
    * @param AccountManagerProfile $accountManagerProfile
    */
    public function map($accountManagerProfile): array
    {
        return [
            $accountManagerProfile->id,
            $accountManagerProfile->file,
            $accountManagerProfile?->user ? $accountManagerProfile->user->username . ' (' . $accountManagerProfile->user->site->name . ')' : 'No user set',
            $accountManagerProfile->edited_at,
            $accountManagerProfile->created_at,
        ];
    }

    public function query()
    {
        return AccountManagerProfile::query()
                            ->with(['user:id,username,site_id', 'user.site:id,name'])
                            ->whereIn('id', $this->accountManagerProfiles)
                            ->orderByDesc('id');
    }
}
