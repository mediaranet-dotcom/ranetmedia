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
        Schema::create('billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Monthly", "Quarterly", "Yearly"
            $table->integer('interval_count'); // e.g., 1 for monthly, 3 for quarterly
            $table->enum('interval_type', ['day', 'week', 'month', 'year'])->default('month');
            $table->integer('billing_day')->default(1); // Day of month to bill (1-31)
            $table->integer('due_days')->default(7); // Days after invoice date for due date
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_cycles');
    }
};
