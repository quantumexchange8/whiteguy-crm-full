<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'site' => 'namba1.com',
            'username' => 'dev4',
            'password' => 'qQ`123456',
            'account_id' => 'X1ZKPCTPP9FR',
            'first_name' => 'dev',
            'last_name' => '',
            'full_legal_name' => 'dev',
            'email' => 'dev4@dev.com',
            'phone_number' => null,
            'address' => '',
            'country_of_citizenship' => '',
            'account_holder' => 'dev',
            'account_type' => 'Individual',
            'customer_type' => '',
            'account_manager' => '',
            'lead_status' => '',
            'client_stage' => '',
            'rank' => 'Normal',
            'remark' => '',
            'previous_broker_name' => '',
            'kyc_status' => '',
            'is_active' => true,
            'has_crm_access' => true,
            'has_leads_access' => true,
            'is_staff' => true,
            'is_superuser' => true,
            'last_login' => Carbon::now()->toDateTimeString(),
        ]);

        // $user->assignRole('staff');
    }
}
