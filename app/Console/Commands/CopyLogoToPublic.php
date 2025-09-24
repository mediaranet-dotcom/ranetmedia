<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyLogoToPublic extends Command
{
    protected $signature = 'logo:copy';
    protected $description = 'Salin logo dari storage ke public/images/logo.png';

    public function handle()
    {
        // Lokasi file asal (ganti nama file jika kamu punya nama lain)
        $sourcePath = storage_path('app/public/logos/01K1M9RJ07HN6XJY8NFA9FBQPR.jpg');

        // Lokasi tujuan
        $targetDir = public_path('images');
        $targetPath = $targetDir . '/logo.png';

        // Buat folder 'public/images' jika belum ada
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
            $this->info("📁 Folder 'public/images' berhasil dibuat.");
        }

        // Cek apakah file asal ada
        if (!File::exists($sourcePath)) {
            $this->error("❌ File sumber tidak ditemukan: " . $sourcePath);
            return 1;
        }

        // Salin file
        if (File::copy($sourcePath, $targetPath)) {
            $this->info("✅ Logo berhasil disalin ke: " . $targetPath);
            $this->info("🔗 Coba buka: http://localhost:8000/images/logo.png");
        } else {
            $this->error("❌ Gagal menyalin file.");
            return 1;
        }

        return 0;
    }
}