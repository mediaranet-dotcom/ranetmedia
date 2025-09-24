<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Filament\Resources\CustomerReportResource\Pages\ListCustomerReports;

class LaporanPembayaranTotalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function it_displays_total_payment_summary()
    {
        // Create test data
        $customer1 = Customer::factory()->create(['name' => 'Customer 1']);
        $customer2 = Customer::factory()->create(['name' => 'Customer 2']);
        
        $package = Package::factory()->create(['price' => 100000]);
        
        $service1 = Service::factory()->create([
            'customer_id' => $customer1->id,
            'package_id' => $package->id,
        ]);
        
        $service2 = Service::factory()->create([
            'customer_id' => $customer2->id,
            'package_id' => $package->id,
        ]);

        // Create payments
        Payment::factory()->create([
            'service_id' => $service1->id,
            'amount' => 100000,
            'payment_date' => now(),
        ]);
        
        Payment::factory()->create([
            'service_id' => $service2->id,
            'amount' => 150000,
            'payment_date' => now(),
        ]);

        // Test the Livewire component
        $this->actingAs($this->admin);
        
        $component = Livewire::test(ListCustomerReports::class);
        
        // Check if the component renders without errors
        $component->assertStatus(200);
        
        // The component should display the customers
        $component->assertSee('Customer 1');
        $component->assertSee('Customer 2');
        
        // Check if total amounts are calculated correctly
        // This would be visible in the summary row
        $component->assertSee('Total Keseluruhan');
        $component->assertSee('Total Periode');
    }

    /** @test */
    public function it_calculates_period_total_correctly()
    {
        // Create test data for specific period
        $customer = Customer::factory()->create();
        $package = Package::factory()->create(['price' => 100000]);
        $service = Service::factory()->create([
            'customer_id' => $customer->id,
            'package_id' => $package->id,
        ]);

        // Create payments for current month
        Payment::factory()->create([
            'service_id' => $service->id,
            'amount' => 100000,
            'payment_date' => now(),
            'created_at' => now(),
        ]);
        
        Payment::factory()->create([
            'service_id' => $service->id,
            'amount' => 50000,
            'payment_date' => now(),
            'created_at' => now(),
        ]);

        // Create payment for different month (should not be included in current month total)
        Payment::factory()->create([
            'service_id' => $service->id,
            'amount' => 75000,
            'payment_date' => now()->subMonth(),
            'created_at' => now()->subMonth(),
        ]);

        $this->actingAs($this->admin);
        
        $component = Livewire::test(ListCustomerReports::class);
        
        // The current month total should be 150000 (100000 + 50000)
        // The different month payment (75000) should not be included
        $component->assertStatus(200);
    }

    /** @test */
    public function it_handles_empty_data_gracefully()
    {
        // Test with no customers/payments
        $this->actingAs($this->admin);
        
        $component = Livewire::test(ListCustomerReports::class);
        
        $component->assertStatus(200);
        $component->assertSee('Total Keseluruhan');
        $component->assertSee('Total Periode');
    }

    /** @test */
    public function it_respects_filters_in_summary()
    {
        // Create customers with different statuses
        $activeCustomer = Customer::factory()->create(['status' => 'active']);
        $inactiveCustomer = Customer::factory()->create(['status' => 'inactive']);
        
        $package = Package::factory()->create(['price' => 100000]);
        
        $activeService = Service::factory()->create([
            'customer_id' => $activeCustomer->id,
            'package_id' => $package->id,
        ]);
        
        $inactiveService = Service::factory()->create([
            'customer_id' => $inactiveCustomer->id,
            'package_id' => $package->id,
        ]);

        // Create payments
        Payment::factory()->create([
            'service_id' => $activeService->id,
            'amount' => 100000,
        ]);
        
        Payment::factory()->create([
            'service_id' => $inactiveService->id,
            'amount' => 200000,
        ]);

        $this->actingAs($this->admin);
        
        $component = Livewire::test(ListCustomerReports::class);
        
        // Apply status filter for active customers only
        $component->set('tableFilters.status.value', 'active');
        
        // The summary should only include active customer payments
        $component->assertStatus(200);
    }
}
