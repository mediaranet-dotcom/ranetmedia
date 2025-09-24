<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'tax_number',
        'business_license',
        'bank_details',
        'logo_path',
        'invoice_settings',
        'email_settings',
        'is_active',
    ];

    protected $casts = [
        'invoice_settings' => 'array',
        'email_settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the active company settings
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first() ?? static::getDefault();
    }

    /**
     * Get default company settings
     */
    public static function getDefault()
    {
        return new static([
            'company_name' => 'RANET Provider',
            'company_address' => 'Alamat Perusahaan Belum Diatur',
            'company_phone' => 'Telepon Belum Diatur',
            'company_email' => 'email@ranet.com',
            'company_website' => 'www.ranet.com',
            'tax_number' => 'NPWP Belum Diatur',
            'business_license' => 'NIB Belum Diatur',
            'bank_details' => 'Detail Bank Belum Diatur',
            'invoice_settings' => [
                'show_logo' => true,
                'show_tax_number' => true,
                'show_business_license' => true,
                'show_bank_details' => true,
                'footer_text' => 'Terima kasih atas kepercayaan Anda menggunakan layanan RANET Provider',
            ],
            'email_settings' => [
                'from_name' => 'RANET Provider',
                'reply_to' => 'noreply@ranet.com',
            ],
            'is_active' => true,
        ]);
    }
}
