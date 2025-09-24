<?php

namespace Database\Seeders;

use App\Models\Odp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OdpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $odps = [
            [
                'name' => 'ODP-001',
                'code' => 'ODP001',
                'description' => 'ODP di area perumahan Griya Asri',
                'address' => 'Jl. Griya Asri No. 15, RT 02/RW 05, Kelurahan Sukamaju',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'area' => 'Sukamaju',
                'district' => 'Depok Timur',
                'total_ports' => 8,
                'used_ports' => 5,
                'available_ports' => 3,
                'odp_type' => '8_port',
                'manufacturer' => 'Huawei',
                'model' => 'FDB-08A',
                'feeder_cable' => 'FC-001',
                'fiber_count' => 24,
                'splitter_ratio' => '1:8',
                'status' => 'active',
                'condition' => 'good',
                'installation_date' => '2024-01-15',
                'last_maintenance' => '2024-06-15',
                'notes' => 'ODP utama untuk area perumahan Griya Asri'
            ],
            [
                'name' => 'ODP-002',
                'code' => 'ODP002',
                'description' => 'ODP di area komersial Jalan Raya',
                'address' => 'Jl. Raya Depok No. 88, RT 01/RW 03, Kelurahan Pancoran Mas',
                'latitude' => -6.205000,
                'longitude' => 106.820000,
                'area' => 'Pancoran Mas',
                'district' => 'Pancoran Mas',
                'total_ports' => 16,
                'used_ports' => 12,
                'available_ports' => 4,
                'odp_type' => '16_port',
                'manufacturer' => 'ZTE',
                'model' => 'FDB-16B',
                'feeder_cable' => 'FC-002',
                'fiber_count' => 48,
                'splitter_ratio' => '1:16',
                'status' => 'active',
                'condition' => 'excellent',
                'installation_date' => '2024-02-20',
                'last_maintenance' => '2024-07-10',
                'notes' => 'ODP untuk area komersial dengan traffic tinggi'
            ],
            [
                'name' => 'ODP-003',
                'code' => 'ODP003',
                'description' => 'ODP di area perkantoran',
                'address' => 'Jl. Margonda Raya No. 200, RT 05/RW 08, Kelurahan Pondok Cina',
                'latitude' => -6.210000,
                'longitude' => 106.825000,
                'area' => 'Pondok Cina',
                'district' => 'Beji',
                'total_ports' => 8,
                'used_ports' => 7,
                'available_ports' => 1,
                'odp_type' => '8_port',
                'manufacturer' => 'Fiberhome',
                'model' => 'FDB-08C',
                'feeder_cable' => 'FC-003',
                'fiber_count' => 24,
                'splitter_ratio' => '1:8',
                'status' => 'active',
                'condition' => 'good',
                'installation_date' => '2024-03-10',
                'last_maintenance' => '2024-05-20',
                'notes' => 'ODP hampir penuh, perlu ekspansi'
            ],
            [
                'name' => 'ODP-004',
                'code' => 'ODP004',
                'description' => 'ODP backup area industri',
                'address' => 'Jl. Industri No. 45, RT 03/RW 06, Kelurahan Limo',
                'latitude' => -6.215000,
                'longitude' => 106.830000,
                'area' => 'Limo',
                'district' => 'Limo',
                'total_ports' => 32,
                'used_ports' => 0,
                'available_ports' => 32,
                'odp_type' => '32_port',
                'manufacturer' => 'Huawei',
                'model' => 'FDB-32A',
                'feeder_cable' => 'FC-004',
                'fiber_count' => 96,
                'splitter_ratio' => '1:32',
                'status' => 'inactive',
                'condition' => 'excellent',
                'installation_date' => '2024-04-05',
                'notes' => 'ODP cadangan untuk ekspansi area industri'
            ]
        ];

        foreach ($odps as $odpData) {
            Odp::create($odpData);
        }
    }
}
