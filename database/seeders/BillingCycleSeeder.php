<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillingCycle;

class BillingCycleSeeder extends Seeder
{
    public function run(): void
    {
        $cycles = [
            [
                'name' => 'Bulanan',
                'interval_count' => 1,
                'interval_type' => 'month',
                'billing_day' => 1,
                'due_days' => 7,
                'is_active' => true,
                'description' => 'Tagihan bulanan - setiap tanggal 1, jatuh tempo 7 hari'
            ],
            [
                'name' => 'Bulanan (Tanggal 15)',
                'interval_count' => 1,
                'interval_type' => 'month',
                'billing_day' => 15,
                'due_days' => 7,
                'is_active' => true,
                'description' => 'Tagihan bulanan - setiap tanggal 15, jatuh tempo 7 hari'
            ],
            [
                'name' => 'Triwulan',
                'interval_count' => 3,
                'interval_type' => 'month',
                'billing_day' => 1,
                'due_days' => 14,
                'is_active' => true,
                'description' => 'Tagihan triwulan - setiap 3 bulan, jatuh tempo 14 hari'
            ],
            [
                'name' => 'Semester',
                'interval_count' => 6,
                'interval_type' => 'month',
                'billing_day' => 1,
                'due_days' => 30,
                'is_active' => true,
                'description' => 'Tagihan semester - setiap 6 bulan, jatuh tempo 30 hari'
            ],
            [
                'name' => 'Tahunan',
                'interval_count' => 1,
                'interval_type' => 'year',
                'billing_day' => 1,
                'due_days' => 30,
                'is_active' => true,
                'description' => 'Tagihan tahunan - setiap tahun, jatuh tempo 30 hari'
            ]
        ];

        foreach ($cycles as $cycle) {
            BillingCycle::firstOrCreate(
                ['name' => $cycle['name']],
                $cycle
            );
        }

        $this->command->info('Billing cycles seeded successfully!');
    }
}
