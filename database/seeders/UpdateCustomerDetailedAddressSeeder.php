<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class UpdateCustomerDetailedAddressSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        
        foreach ($customers as $customer) {
            // Update customer dengan alamat detail contoh
            if ($customer->name === 'Test Customer') {
                $customer->update([
                    'province' => 'Jawa Tengah',
                    'regency' => 'Kabupaten Semarang',
                    'district' => 'Ungaran Barat',
                    'village' => 'Lerep',
                    'hamlet' => 'Krajan',
                    'rt' => '01',
                    'rw' => '02',
                    'postal_code' => '50552',
                    'address_notes' => 'Dekat Masjid Al-Ikhlas, sebelah warung Bu Sari'
                ]);
            } elseif ($customer->name === 'Budi Santoso') {
                $customer->update([
                    'province' => 'Jawa Tengah',
                    'regency' => 'Kabupaten Semarang',
                    'district' => 'Ungaran Timur',
                    'village' => 'Sukamaju',
                    'hamlet' => 'Tengah',
                    'rt' => '03',
                    'rw' => '05',
                    'postal_code' => '50553',
                    'address_notes' => 'Depan SD Negeri 1 Sukamaju'
                ]);
            } elseif ($customer->name === 'Siti Nurhaliza') {
                $customer->update([
                    'province' => 'Jawa Tengah',
                    'regency' => 'Kabupaten Semarang',
                    'district' => 'Bergas',
                    'village' => 'Pancoran Mas',
                    'hamlet' => 'Selatan',
                    'rt' => '02',
                    'rw' => '04',
                    'postal_code' => '50554',
                    'address_notes' => 'Belakang Puskesmas Bergas'
                ]);
            } else {
                // Default untuk customer lain
                $customer->update([
                    'province' => 'Jawa Tengah',
                    'regency' => 'Kabupaten Semarang',
                    'district' => 'Ungaran Barat',
                    'village' => 'Lerep',
                    'hamlet' => 'Krajan',
                    'rt' => '01',
                    'rw' => '01',
                    'postal_code' => '50552',
                    'address_notes' => 'Area coverage ISP'
                ]);
            }
            
            $this->command->info("Updated detailed address for customer: {$customer->name}");
        }
        
        $this->command->info("All customers updated with detailed address information.");
    }
}
