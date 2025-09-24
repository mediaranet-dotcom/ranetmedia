<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up any existing duplicates
        $this->cleanupDuplicateInvoices();
        
        // Add unique constraint to prevent future duplicates
        Schema::table('invoices', function (Blueprint $table) {
            $table->unique(['service_id', 'billing_period_start', 'billing_period_end'], 'unique_service_billing_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('unique_service_billing_period');
        });
    }

    /**
     * Clean up duplicate invoices, keeping only the oldest one for each service+period
     */
    private function cleanupDuplicateInvoices(): void
    {
        // Find duplicates
        $duplicates = DB::select("
            SELECT service_id, billing_period_start, billing_period_end, COUNT(*) as count
            FROM invoices 
            GROUP BY service_id, billing_period_start, billing_period_end 
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicates as $duplicate) {
            echo "Found {$duplicate->count} duplicates for service {$duplicate->service_id} period {$duplicate->billing_period_start}\n";
            
            // Get all invoices for this service+period, ordered by created_at (keep oldest)
            $invoices = DB::select("
                SELECT id, invoice_number, created_at 
                FROM invoices 
                WHERE service_id = ? AND billing_period_start = ? AND billing_period_end = ?
                ORDER BY created_at ASC
            ", [$duplicate->service_id, $duplicate->billing_period_start, $duplicate->billing_period_end]);

            // Keep the first (oldest) invoice, delete the rest
            for ($i = 1; $i < count($invoices); $i++) {
                $invoiceToDelete = $invoices[$i];
                echo "Deleting duplicate invoice: {$invoiceToDelete->invoice_number}\n";
                
                // Delete related records first
                DB::delete("DELETE FROM invoice_items WHERE invoice_id = ?", [$invoiceToDelete->id]);
                DB::delete("DELETE FROM payments WHERE invoice_id = ?", [$invoiceToDelete->id]);
                
                // Delete the invoice
                DB::delete("DELETE FROM invoices WHERE id = ?", [$invoiceToDelete->id]);
            }
        }
    }
};
