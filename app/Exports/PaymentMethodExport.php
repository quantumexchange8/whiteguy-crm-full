<?php

namespace App\Exports;

use App\Models\PaymentMethod;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class PaymentMethodExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $paymentMethods;
    
    public function __construct(array $paymentMethods)
    {
        $this->paymentMethods = $paymentMethods;
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
    * @param PaymentMethod $paymentMethod
    */
    public function map($paymentMethod): array
    {
        dd($paymentMethod, encrypt($paymentMethod->account_number), PaymentMethod::has('sitePaymentMethod'));

        return [
            $paymentMethod->id,
            $paymentMethod->title,
            PaymentMethod::has('sitePaymentMethod'),
            $paymentMethod->bank_name,
            $paymentMethod->account_name,
            $paymentMethod->account_number,
        ];
    }

    public function query()
    {
        return PaymentMethod::query()
                            // ->with(['user:id,username,site_id', 'user.site:id,name'])
                            ->whereIn('id', $this->paymentMethods)
                            ->orderByDesc('id');
    }
}
