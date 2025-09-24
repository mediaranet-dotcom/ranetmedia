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
        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('level'); // 1=Low, 2=Medium, 3=High, 4=Critical
            $table->string('color', 7); // Hex color for UI
            $table->string('icon')->default('heroicon-o-exclamation-circle');
            $table->integer('sla_hours'); // SLA response time in hours
            $table->integer('escalation_hours')->nullable(); // Auto-escalate after X hours
            $table->boolean('requires_immediate_notification')->default(false);
            $table->boolean('send_whatsapp_notification')->default(false);
            $table->boolean('send_email_notification')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_priorities');
    }
};
