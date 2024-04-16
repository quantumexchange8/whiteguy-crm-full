<?php

namespace App\Exports;

use App\Models\Notification;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NotificationsExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $notifications;
    
    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'Title',
            'Description',
            'Notification Type',
            'Attachment',
            'Notification Viewed?',
            'User',
            'Edited At',
            'Created At',
        ];
    }

    public function query()
    {
        return Notification::query()->whereIn('id', $this->notifications);
    }
}
