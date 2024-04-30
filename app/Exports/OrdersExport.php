<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithDefaultStyles
{
    use Exportable;

    protected $orders;
    
    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'Trade Id',
            'Date',
            'Action Type',
            'Stock Type',
            'Stock',
            'Unit Price',
            'Quantity',
            'Total Price',
            'Current Unit Price',
            'Profit',
            'Status',
            'Edited At',
            'Created At',
            'User / Client',
            'Is Deleted',
            'Limb Stage',
            'Confirmation Name',
            'Confirmed At',
        ];
    }

    /**
    * @param Order $order
    */
    public function map($order): array
    {
        $statusArray = [ 
            "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
            "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required"
        ];

        $limbStageArray  = [ 
            "ALLO", "Allo + docs", "Tt", "Cleared", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
            "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" 
        ];

        return [
            $order->id,
            $order->trade_id,
            $order->date,
            $order->action_type,
            $order->stock_type,
            $order->stock,
            number_format((float)$order->unit_price, 2, '.', ''),
            number_format((float)$order->quantity, 2, '.', ''),
            number_format((float)($order->unit_price * $order->quantity), 2, '.', ''),
            number_format((float)$order->current_unit_price, 2, '.', ''),
            number_format((float)$order->profit, 2, '.', ''),
            $order->status ? $statusArray[$order->status - 1] : '',
            $order->edited_at,
            $order->created_at,
            $order->user_id ? $order->user->username . ' (' . $order->user->site->name . ')' : '-',
            (!$order->is_deleted) ? 'FALSE' : $order->is_deleted,
            $order->limb_stage ? $limbStageArray[$order->limb_stage - 1] : '',
            $order->confirmation_name,
            $order->confirmed_at,
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
        return Order::query()->whereIn('id', $this->orders);
    }
}
