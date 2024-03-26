<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;
    
    protected $table = "core_saleorder";

    public $timestamps = false;

    protected $fillable = [
        'public_id',
        'written_date',
        'vc',
        'room_number',
        'allo',
        'allo_name',
        'sm_number',
        'gm_number',
        'fm_number',
        'lm_number',
        'ao_1',
        'ao_1_name',
        'ao_2',
        'ao_2_name',
        'bonus_comment',
        'currency_pair',
        'exchange_rate',
        'registered_name',
        'contact_name',
        'office_number_1',
        'office_number_2',
        'home_number',
        'mobile_number',
        'fax_number',
        'date_of_birth',
        'email',
        'address_1',
        'address_2',
        'city',
        'country',
        'exit_time_frame',
        'sell_price',
        'allocation_officer',
        'trade_plus',
        'settlement_date',
        'factory',
        'pay_via',
        'allo_comment',
        'docs_received',
        'tc_sent',
        'tt_received',
        'edited_at',
        'created_at',
        'balance_due',
        'exchanged_balance_due',
        'site_id',
        'se_name',
        'se_number',
        'created_by_id',
    ];
}
