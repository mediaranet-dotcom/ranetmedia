<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateCompanyLogo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:update-logo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update company logo path in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Mencari file logo...');

        // Cari file logo JPG terbaru
        $logoFiles = glob(storage_path('app/public/logos/*.jpg'));

        if (empty($logoFiles)) {
            $this->error('❌ Tidak ada file JPG di storage/app/public/logos/');
            return 1;
        }

        // Ambil file terbaru
        $latestLogo = end($logoFiles);
        $logoPath = 'logos/' . basename($latestLogo);

        $this->info("📁 File logo ditemukan: " . basename($latestLogo));

        // Update company setting
        $company = \App\Models\CompanySetting::first();

        if (!$company) {
            $this->error('❌ Company setting tidak ditemukan!');
            return 1;
        }

        $company->logo_path = $logoPath;
        $company->save();

        $this->info("✅ Logo path berhasil diupdate!");
        $this->info("🏢 Company: {$company->company_name}");
        $this->info("🖼️ Logo: {$company->logo_path}");
        $this->info("🔗 URL: " . asset('storage/' . $company->logo_path));

        // Clear cache
        $this->call('cache:clear');
        $this->call('config:clear');

        $this->info("🎉 Selesai! Refresh browser untuk melihat perubahan.");

        return 0;
    }
}
