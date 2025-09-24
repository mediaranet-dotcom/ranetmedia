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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Hex color for UI
            $table->string('icon')->default('heroicon-o-ticket'); // Icon for UI
            $table->integer('default_priority_level')->default(2); // 1=Low, 2=Medium, 3=High, 4=Critical
            $table->integer('default_sla_hours')->default(24); // Default SLA in hours
            $table->boolean('requires_technical_team')->default(false);
            $table->boolean('auto_assign_to_department')->default(false);
            $table->string('department')->nullable(); // technical, billing, sales, support
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
