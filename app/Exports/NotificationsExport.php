<?php

namespace App\Exports;

use App\Models\Notification;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;

class NotificationsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithDefaultStyles
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
            'Edited At',
            'Created At',
            'User',
        ];
    }

    /**
    * @param Notification $notification
    */
    public function map($notification): array
    {
        if ($notification->notification_type === 'PRIVATE_MESSAGE') {
            $notification->notification_type = 'Custom notification';
        }
        if ($notification->notification_type === 'NEW_ANNOUNCEMENT') {
            $notification->notification_type = 'New announcement';
        }
        
        return [
            $notification->id,
            $notification->title,
            $notification->description,
            $notification->notification_type,
            $notification->attachment,
            (!$notification->is_read) ? 'FALSE' : $notification->is_read,
            $notification->edited_at,
            $notification->created_at,
            $notification->user_id ? $notification->user->username . ' (' . $notification->user->site->name . ')' : '-',
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
        return Notification::query()
                            ->with([
                                'user:id,username,site_id',
                                'user.site:id,name'
                            ])
                            ->orderByDesc('id')
                            ->whereIn('id', $this->notifications);
    }
}
