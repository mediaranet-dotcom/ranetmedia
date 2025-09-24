<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'Rendah',
                'slug' => 'rendah',
                'description' => 'Masalah non-urgent yang bisa ditangani dalam waktu normal',
                'level' => 1,
                'color' => '#10B981',
                'icon' => 'heroicon-o-minus-circle',
                'sla_hours' => 72,
                'escalation_hours' => 96,
                'requires_immediate_notification' => false,
                'send_whatsapp_notification' => false,
                'send_email_notification' => true,
            ],
            [
                'name' => 'Sedang',
                'slug' => 'sedang',
                'description' => 'Masalah standar yang perlu ditangani dalam waktu wajar',
                'level' => 2,
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-exclamation-circle',
                'sla_hours' => 24,
                'escalation_hours' => 36,
                'requires_immediate_notification' => false,
                'send_whatsapp_notification' => false,
                'send_email_notification' => true,
            ],
            [
                'name' => 'Tinggi',
                'slug' => 'tinggi',
                'description' => 'Masalah penting yang mempengaruhi layanan customer',
                'level' => 3,
                'color' => '#F97316',
                'icon' => 'heroicon-o-exclamation-triangle',
                'sla_hours' => 8,
                'escalation_hours' => 12,
                'requires_immediate_notification' => true,
                'send_whatsapp_notification' => true,
                'send_email_notification' => true,
            ],
            [
                'name' => 'Kritis',
                'slug' => 'kritis',
                'description' => 'Masalah urgent yang membutuhkan penanganan segera',
                'level' => 4,
                'color' => '#EF4444',
                'icon' => 'heroicon-o-fire',
                'sla_hours' => 2,
                'escalation_hours' => 4,
                'requires_immediate_notification' => true,
                'send_whatsapp_notification' => true,
                'send_email_notification' => true,
            ],
        ];

        foreach ($priorities as $priority) {
            DB::table('ticket_priorities')->insert(array_merge($priority, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
