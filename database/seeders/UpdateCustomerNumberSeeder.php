<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class UpdateCustomerNumberSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::whereNull('customer_number')->orWhere('customer_number', '')->get();
        
        foreach ($customers as $index => $customer) {
            $yearMonth = $customer->created_at ? $customer->created_at->format('Ym') : now()->format('Ym');
            $customerNumber = sprintf('RANET-%s-%04d', $yearMonth, $index + 1);
            
            // Check if this number already exists
            while (Customer::where('customer_number', $customerNumber)->exists()) {
                $index++;
                $customerNumber = sprintf('RANET-%s-%04d', $yearMonth, $index + 1);
            }
            
            $customer->update(['customer_number' => $customerNumber]);
            $this->command->info("Updated customer {$customer->name} with number: {$customerNumber}");
        }
        
        $this->command->info('Customer numbers updated successfully!');
    }
}
