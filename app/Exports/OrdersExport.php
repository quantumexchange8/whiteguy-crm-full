<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromQuery, WithHeadings
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
            'Current Price',
            'Profit',
            'Status',
            'Confirmed At',
            'Confirmation Name',
            'Limb Stage',
            'User Link',
            'Send Notification',
            'Notification Title',
            'Notification Description',
            'Last Login',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function query()
    {
        return Order::query()->whereIn('id', $this->orders);
    }
}
