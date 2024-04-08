<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\XeroSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class XeroApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(['id' => 1],[
            'xero_redirect_url' => 'https://axiomeds.test/api/setting/xeroCallback',
            'xero_client_secret'=> 'pZTNb_sKABMo_6hUJdhvkmWjndWdv3RhoOzmZD_ch0',
            'xero_client_id'=> '50E9147AC48947E7B6BA1A6549C56520'
        ]);  
        XeroSetting::updateOrCreate(['id' => 1],[
            'token' => '',
            'expires' => '',
            'tenant_id' => '',
            'refresh_token' => '',
            'id_token' => ''
        ]);  
    }
}
