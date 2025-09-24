<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CompanySetting::create([
            'company_name' => 'RANET Provider',
            'company_address' => "Jl. Teknologi Digital No. 123\nJakarta Selatan 12345\nIndonesia",
            'company_phone' => '(021) 1234-5678',
            'company_email' => 'info@ranetprovider.com',
            'company_website' => 'www.ranetprovider.com',
            'tax_number' => '12.345.678.9-012.000',
            'business_license' => '1234567890123456',
            'bank_details' => "Bank BCA: 1234567890\nBank Mandiri: 0987654321\nAtas Nama: PT RANET Provider\n\nE-Wallet:\nGoPay/OVO: 081234567890\nDANA: 081234567890",
            'invoice_settings' => [
                'show_logo' => true,
                'show_tax_number' => true,
                'show_business_license' => true,
                'show_bank_details' => true,
                'footer_text' => 'Terima kasih atas kepercayaan Anda menggunakan layanan RANET Provider. Untuk informasi lebih lanjut hubungi customer service kami.',
            ],
            'email_settings' => [
                'from_name' => 'RANET Provider',
                'reply_to' => 'noreply@ranetprovider.com',
            ],
            'is_active' => true,
        ]);
    }
}
