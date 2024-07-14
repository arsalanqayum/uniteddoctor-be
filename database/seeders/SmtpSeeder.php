<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::where('id', 1)->update([
            'email_provider' => 'smtp',
            'smtp_mail_host' => 'sandbox.smtp.mailtrap.io',
            'smtp_mail_port' => '25',
            'smtp_mail_username' => '4a0c21c86bae04',
            'smtp_mail_password' => '24937536568513',
            'smtp_mail_encryption' => 'tls',
            'smtp_mail_from_address' => 'hello@example.com',
            'smtp_mail_from_name' => 'Axiomed',
        ]);
    }
}
