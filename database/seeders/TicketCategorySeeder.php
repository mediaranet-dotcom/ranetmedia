<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gangguan Internet',
                'slug' => 'gangguan-internet',
                'description' => 'Masalah koneksi internet, lambat, atau tidak bisa akses',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-wifi',
                'default_priority_level' => 3,
                'default_sla_hours' => 4,
                'requires_technical_team' => true,
                'auto_assign_to_department' => true,
                'department' => 'technical',
                'sort_order' => 1,
            ],
            [
                'name' => 'Instalasi Baru',
                'slug' => 'instalasi-baru',
                'description' => 'Permintaan instalasi layanan internet baru',
                'color' => '#10B981',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'default_priority_level' => 2,
                'default_sla_hours' => 48,
                'requires_technical_team' => true,
                'auto_assign_to_department' => true,
                'department' => 'technical',
                'sort_order' => 2,
            ],
            [
                'name' => 'Tagihan & Pembayaran',
                'slug' => 'tagihan-pembayaran',
                'description' => 'Pertanyaan tentang tagihan, pembayaran, atau invoice',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-banknotes',
                'default_priority_level' => 2,
                'default_sla_hours' => 24,
                'requires_technical_team' => false,
                'auto_assign_to_department' => true,
                'department' => 'billing',
                'sort_order' => 3,
            ],
            [
                'name' => 'Upgrade/Downgrade Paket',
                'slug' => 'upgrade-downgrade',
                'description' => 'Permintaan perubahan paket layanan',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-arrow-trending-up',
                'default_priority_level' => 2,
                'default_sla_hours' => 24,
                'requires_technical_team' => true,
                'auto_assign_to_department' => true,
                'department' => 'sales',
                'sort_order' => 4,
            ],
            [
                'name' => 'Pemutusan Layanan',
                'slug' => 'pemutusan-layanan',
                'description' => 'Permintaan pemutusan atau suspend layanan',
                'color' => '#6B7280',
                'icon' => 'heroicon-o-x-circle',
                'default_priority_level' => 2,
                'default_sla_hours' => 24,
                'requires_technical_team' => false,
                'auto_assign_to_department' => true,
                'department' => 'billing',
                'sort_order' => 5,
            ],
            [
                'name' => 'Keluhan Kecepatan',
                'slug' => 'keluhan-kecepatan',
                'description' => 'Keluhan tentang kecepatan internet tidak sesuai',
                'color' => '#F97316',
                'icon' => 'heroicon-o-signal',
                'default_priority_level' => 3,
                'default_sla_hours' => 8,
                'requires_technical_team' => true,
                'auto_assign_to_department' => true,
                'department' => 'technical',
                'sort_order' => 6,
            ],
            [
                'name' => 'Masalah Perangkat',
                'slug' => 'masalah-perangkat',
                'description' => 'Masalah dengan router, modem, atau perangkat lainnya',
                'color' => '#06B6D4',
                'icon' => 'heroicon-o-cpu-chip',
                'default_priority_level' => 2,
                'default_sla_hours' => 12,
                'requires_technical_team' => true,
                'auto_assign_to_department' => true,
                'department' => 'technical',
                'sort_order' => 7,
            ],
            [
                'name' => 'Informasi Umum',
                'slug' => 'informasi-umum',
                'description' => 'Pertanyaan umum tentang layanan',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-information-circle',
                'default_priority_level' => 1,
                'default_sla_hours' => 24,
                'requires_technical_team' => false,
                'auto_assign_to_department' => true,
                'department' => 'support',
                'sort_order' => 8,
            ],
            [
                'name' => 'Komplain Layanan',
                'slug' => 'komplain-layanan',
                'description' => 'Komplain atau ketidakpuasan terhadap layanan',
                'color' => '#DC2626',
                'icon' => 'heroicon-o-exclamation-triangle',
                'default_priority_level' => 3,
                'default_sla_hours' => 6,
                'requires_technical_team' => false,
                'auto_assign_to_department' => true,
                'department' => 'support',
                'sort_order' => 9,
            ],
            [
                'name' => 'Permintaan Fitur',
                'slug' => 'permintaan-fitur',
                'description' => 'Saran atau permintaan fitur baru',
                'color' => '#059669',
                'icon' => 'heroicon-o-light-bulb',
                'default_priority_level' => 1,
                'default_sla_hours' => 72,
                'requires_technical_team' => false,
                'auto_assign_to_department' => false,
                'department' => null,
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('ticket_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
