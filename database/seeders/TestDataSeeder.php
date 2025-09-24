<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Service;
use App\Models\Odp;
use App\Models\Payment;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create additional customers
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, RT 01/RW 02, Sukamaju',
                'identity_number' => '3201234567890001',
                'status' => 'active',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'phone' => '081234567891',
                'address' => 'Jl. Pancasila No. 45, RT 03/RW 04, Pancoran Mas',
                'identity_number' => '3201234567890002',
                'status' => 'active',
            ],
            [
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad@example.com',
                'phone' => '081234567892',
                'address' => 'Jl. Kemerdekaan No. 67, RT 02/RW 03, Beji',
                'identity_number' => '3201234567890003',
                'status' => 'active',
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi@example.com',
                'phone' => '081234567893',
                'address' => 'Jl. Proklamasi No. 89, RT 04/RW 05, Limo',
                'identity_number' => '3201234567890004',
                'status' => 'suspended',
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi@example.com',
                'phone' => '081234567894',
                'address' => 'Jl. Diponegoro No. 12, RT 01/RW 01, Sukamaju',
                'identity_number' => '3201234567890005',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::firstOrCreate(['email' => $customerData['email']], $customerData);
        }

        // Get packages
        $packages = Package::all();
        if ($packages->isEmpty()) {
            // Create basic packages if none exist
            $packageData = [
                ['name' => 'Basic 10 Mbps', 'speed' => '10 Mbps', 'price' => 150000, 'description' => 'Paket internet basic untuk rumah tangga'],
                ['name' => 'Standard 25 Mbps', 'speed' => '25 Mbps', 'price' => 250000, 'description' => 'Paket internet standard untuk keluarga'],
                ['name' => 'Premium 50 Mbps', 'speed' => '50 Mbps', 'price' => 400000, 'description' => 'Paket internet premium untuk bisnis kecil'],
            ];

            foreach ($packageData as $pkg) {
                Package::firstOrCreate(['name' => $pkg['name']], $pkg);
            }
            $packages = Package::all();
        }

        // Get ODPs
        $odps = Odp::all();
        $customers = Customer::all();

        // Create services (connections between customers and ODPs)
        $serviceData = [
            [
                'customer_id' => $customers->where('name', 'Budi Santoso')->first()?->id,
                'package_id' => $packages->first()?->id,
                'odp_id' => $odps->where('name', 'ODP-001')->first()?->id,
                'odp_port' => 1,
                'fiber_cable_color' => 'blue',
                'signal_strength' => -18.5,
                'installation_notes' => 'Instalasi normal, signal bagus',
                'ip_address' => '192.168.1.100',
                'router_name' => 'RT-001',
                'start_date' => now()->subMonths(6),
                'status' => 'active',
            ],
            [
                'customer_id' => $customers->where('name', 'Siti Nurhaliza')->first()?->id,
                'package_id' => $packages->skip(1)->first()?->id,
                'odp_id' => $odps->where('name', 'ODP-001')->first()?->id,
                'odp_port' => 2,
                'fiber_cable_color' => 'green',
                'signal_strength' => -22.1,
                'installation_notes' => 'Instalasi dengan sedikit kendala, signal stabil',
                'ip_address' => '192.168.1.101',
                'router_name' => 'RT-002',
                'start_date' => now()->subMonths(4),
                'status' => 'active',
            ],
            [
                'customer_id' => $customers->where('name', 'Ahmad Rahman')->first()?->id,
                'package_id' => $packages->skip(2)->first()?->id,
                'odp_id' => $odps->where('name', 'ODP-002')->first()?->id,
                'odp_port' => 1,
                'fiber_cable_color' => 'orange',
                'signal_strength' => -19.8,
                'installation_notes' => 'Instalasi premium, signal excellent',
                'ip_address' => '192.168.2.100',
                'router_name' => 'RT-003',
                'start_date' => now()->subMonths(3),
                'status' => 'active',
            ],
            [
                'customer_id' => $customers->where('name', 'Dewi Sartika')->first()?->id,
                'package_id' => $packages->first()?->id,
                'odp_id' => $odps->where('name', 'ODP-002')->first()?->id,
                'odp_port' => 2,
                'fiber_cable_color' => 'red',
                'signal_strength' => -31.2,
                'installation_notes' => 'Signal lemah, perlu maintenance',
                'ip_address' => '192.168.2.101',
                'router_name' => 'RT-004',
                'start_date' => now()->subMonths(2),
                'status' => 'suspended',
            ],
            [
                'customer_id' => $customers->where('name', 'Rudi Hermawan')->first()?->id,
                'package_id' => $packages->skip(1)->first()?->id,
                'odp_id' => $odps->where('name', 'ODP-001')->first()?->id,
                'odp_port' => 3,
                'fiber_cable_color' => 'yellow',
                'signal_strength' => -25.7,
                'installation_notes' => 'Instalasi standard, monitoring signal',
                'ip_address' => '192.168.1.102',
                'router_name' => 'RT-005',
                'start_date' => now()->subMonth(),
                'status' => 'active',
            ],
        ];

        foreach ($serviceData as $service) {
            if ($service['customer_id'] && $service['package_id'] && $service['odp_id']) {
                Service::firstOrCreate([
                    'customer_id' => $service['customer_id'],
                    'odp_id' => $service['odp_id'],
                    'odp_port' => $service['odp_port'],
                ], $service);
            }
        }

        // Update ODP port usage
        foreach ($odps as $odp) {
            $odp->updatePortUsage();
        }

        // Create some payment records
        $services = Service::all();
        foreach ($services as $service) {
            if ($service->status === 'active') {
                // Create payments for last 3 months
                for ($i = 0; $i < 3; $i++) {
                    Payment::firstOrCreate([
                        'service_id' => $service->id,
                        'year' => now()->subMonths($i)->year,
                        'month' => now()->subMonths($i)->month,
                    ], [
                        'amount' => $service->package->price,
                        'payment_date' => now()->subMonths($i)->startOfMonth()->addDays(rand(1, 28)),
                        'payment_method' => ['cash', 'bank_transfer', 'e_wallet'][rand(0, 2)],
                        'reference_number' => 'PAY-' . now()->subMonths($i)->format('Ym') . '-' . str_pad($service->id, 4, '0', STR_PAD_LEFT),
                        'notes' => 'Pembayaran bulanan',
                    ]);
                }
            }
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('Customers: ' . Customer::count());
        $this->command->info('Services: ' . Service::count());
        $this->command->info('Payments: ' . Payment::count());
    }
}
