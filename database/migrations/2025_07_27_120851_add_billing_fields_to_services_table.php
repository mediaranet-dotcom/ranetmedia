<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('billing_cycle_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('billing_day')->default(1); // Day of month to bill
            $table->date('next_billing_date')->nullable();
            $table->date('last_billed_date')->nullable();
            $table->decimal('monthly_fee', 10, 2)->nullable(); // Override package price if needed
            $table->boolean('auto_billing')->default(true);

            $table->index('next_billing_date');
            $table->index('auto_billing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['billing_cycle_id']);
            $table->dropIndex(['next_billing_date']);
            $table->dropIndex(['auto_billing']);
            $table->dropColumn([
                'billing_cycle_id',
                'billing_day',
                'next_billing_date',
                'last_billed_date',
                'monthly_fee',
                'auto_billing'
            ]);
        });
    }
};
