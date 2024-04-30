<?php

namespace App\Exports;

use App\Models\SaleOrder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;

class SaleOrdersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithDefaultStyles
{
    use Exportable;

    protected $saleOrders;
    
    public function __construct(array $saleOrders)
    {
        $this->saleOrders = $saleOrders;
    }
    
    public function headings(): array
    {
        return [
            'ID', 
            'Written Date',
            'Vc',
            'Room Number',
            'Allo',
            'Allo Name',
            'Sm Number',
            'Gm Number',
            'Fm Number',
            'Lm Number',
            'Ao 1',
            'Ao 1 Name',
            'Ao 2',
            'Ao 2 Name',
            'Bonus Comment',
            'Currency Pair',
            'Exchange Rate',
            'Registered Name',
            'Contact Name',
            'Office Number 1',
            'Office Number 2',
            'Home Number',
            'Mobile Number',
            'Fax Number',
            'Date of Birth',
            'Email',
            'Address 1',
            'Address 2',
            'City',
            'Country',
            'Exit Time Frame',
            'Sell Price',
            'Allocation Officer',
            'Trade Plus',
            'Settlement Date',
            'Factory',
            'Pay Via',
            'Allo Comment',
            'Docs Received',
            'Tc Sent',
            'Tt Received',
            'Edited At',
            'Created At',
            'Balance Due',
            'Exchanged Balance Due',
            'Site Id',
            'Se Name',
            'Se Number',
            'Created By',
        ];
    }

    /**
    * @param SaleOrder $saleOrder
    */
    public function map($saleOrder): array
    {
        return [
            $saleOrder->id,
            $saleOrder->written_date,
            $saleOrder->vc,
            $saleOrder->room_number,
            $saleOrder->allo,
            $saleOrder->allo_name,
            $saleOrder->sm_number,
            $saleOrder->gm_number,
            $saleOrder->fm_number,
            $saleOrder->lm_number,
            $saleOrder->ao_1,
            $saleOrder->ao_1_name,
            $saleOrder->ao_2,
            $saleOrder->ao_2_name,
            $saleOrder->bonus_comment,
            $saleOrder->currency_pair,
            number_format((float)$saleOrder->exchange_rate, 2, '.', ''),
            $saleOrder->registered_name,
            $saleOrder->contact_name,
            $saleOrder->office_number_1,
            $saleOrder->office_number_2,
            $saleOrder->home_number,
            $saleOrder->mobile_number,
            $saleOrder->fax_number,
            $saleOrder->date_of_birth,
            $saleOrder->email,
            $saleOrder->address_1,
            $saleOrder->address_2,
            $saleOrder->city,
            $saleOrder->country,
            $saleOrder->exit_time_frame,
            $saleOrder->sell_price,
            $saleOrder->allocation_officer,
            $saleOrder->trade_plus,
            $saleOrder->settlement_date,
            $saleOrder->factory,
            $saleOrder->pay_via,
            $saleOrder->allo_comment,
            $saleOrder->docs_received,
            $saleOrder->tc_sent,
            $saleOrder->tt_received,
            $saleOrder->edited_at,
            $saleOrder->created_at,
            number_format(((float)$saleOrder->balance_due), 2, '.', ''),
            number_format(((float)$saleOrder->exchanged_balance_due), 2, '.', ''),
            $saleOrder->site_id ? $saleOrder->site->domain . ' (' . $saleOrder->site->name . ')' : 'No Site Set',
            $saleOrder->se_name,
            $saleOrder->se_number,
            $saleOrder->created_by_id ? $saleOrder->creator->username . ' (' . $saleOrder->creator->site->name . ')' : '-',
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
        return SaleOrder::query()
                        ->with([
                            'site',
                            'creator:id,username,site_id',
                            'creator.site:id,name',
                        ])
                        ->orderByDesc('id')
                        ->whereIn('id', $this->saleOrders);
    }
}
