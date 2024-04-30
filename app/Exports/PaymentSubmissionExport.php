<?php

namespace App\Exports;

use App\Models\PaymentSubmission;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;

class PaymentSubmissionExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithDefaultStyles
{
    use Exportable;

    protected $paymentSubmissions;
    
    public function __construct(array $paymentSubmissions)
    {
        $this->paymentSubmissions = $paymentSubmissions;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'Date', 
            'Amount',
            'Converted Amount',
            'User Memo',
            'Admin Memo',
            'Admin Remark',
            'Status',
            'Approved At',
            'Edited At',
            'Created At',
            'Payment Method',
            'User', 
        ];
    }

    /**
    * @param PaymentSubmission $paymentSubmission
    */
    public function map($paymentSubmission): array
    {
        $statusArray = [ 
            "Pending", "Approved", "Cancelled"
        ];
        
        return [
            $paymentSubmission->id,
            $paymentSubmission->date,
            number_format((float)$paymentSubmission->amount, 2, '.', ''),
            number_format((float)$paymentSubmission->converted_amount, 2, '.', ''),
            $paymentSubmission->user_memo,
            $paymentSubmission->admin_memo,
            $paymentSubmission->admin_remark,
            $paymentSubmission->status ? $statusArray[$paymentSubmission->status - 1] : '',
            $paymentSubmission->approved_at,
            $paymentSubmission->edited_at,
            $paymentSubmission->created_at,
            $paymentSubmission->payment_method_id ? $paymentSubmission->paymentMethod->title : '',
            $paymentSubmission->user_id ? $paymentSubmission->user->username . ' (' . $paymentSubmission->user->site->name . ')' : '-',
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
        return PaymentSubmission::query()
                            ->with([
                                'user:id,username,site_id',
                                'user.site:id,name',
                                'paymentMethod:id,title'
                            ])
                            ->orderByDesc('id')
                            ->whereIn('id', $this->paymentSubmissions);
    }
}
