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
        // First, update any existing NULL subtotal values to 0
        DB::table('invoices')
            ->whereNull('subtotal')
            ->update(['subtotal' => 0]);

        // Ensure the subtotal column has proper constraints
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->change();
        });

        // Also ensure invoice_items total_price is not null
        DB::table('invoice_items')
            ->whereNull('total_price')
            ->update([
                'total_price' => DB::raw('quantity * unit_price')
            ]);

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('total_price', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this fix
    }
};
