<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket Basic',
                'speed' => '10 Mbps',
                'price' => 150000,
                'description' => 'Paket internet basic untuk kebutuhan sehari-hari. Cocok untuk browsing, email, dan streaming video SD.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Premium',
                'speed' => '25 Mbps',
                'price' => 250000,
                'description' => 'Paket internet premium untuk kebutuhan keluarga. Cocok untuk streaming HD, gaming, dan work from home.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Business',
                'speed' => '50 Mbps',
                'price' => 500000,
                'description' => 'Paket internet business untuk kebutuhan bisnis. Cocok untuk kantor, streaming 4K, dan multiple devices.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Ultra',
                'speed' => '100 Mbps',
                'price' => 750000,
                'description' => 'Paket internet ultra untuk kebutuhan enterprise. Cocok untuk server, cloud computing, dan high-performance applications.',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
