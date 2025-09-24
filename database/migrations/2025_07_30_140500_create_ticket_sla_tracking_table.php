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
        Schema::create('ticket_sla_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            
            // SLA Metrics
            $table->datetime('sla_start_time'); // When SLA clock started
            $table->datetime('sla_due_time'); // When response/resolution is due
            $table->datetime('first_response_time')->nullable(); // When first response was given
            $table->datetime('resolution_time')->nullable(); // When ticket was resolved
            
            // Calculated durations (in minutes)
            $table->integer('response_time_minutes')->nullable(); // Time to first response
            $table->integer('resolution_time_minutes')->nullable(); // Time to resolution
            $table->integer('total_business_hours')->nullable(); // Business hours only
            $table->integer('total_calendar_hours')->nullable(); // Including weekends/holidays
            
            // SLA Status
            $table->boolean('response_sla_met')->nullable(); // Was response SLA met?
            $table->boolean('resolution_sla_met')->nullable(); // Was resolution SLA met?
            $table->integer('response_sla_breach_minutes')->nullable(); // How many minutes over SLA
            $table->integer('resolution_sla_breach_minutes')->nullable(); // How many minutes over SLA
            
            // Pause/Resume tracking (for pending customer responses)
            $table->datetime('sla_paused_at')->nullable();
            $table->datetime('sla_resumed_at')->nullable();
            $table->integer('total_paused_minutes')->default(0);
            $table->text('pause_reason')->nullable();
            
            // Business hours configuration (JSON)
            $table->json('business_hours_config')->nullable(); // Store business hours used for calculation
            
            // Escalation tracking
            $table->boolean('was_escalated')->default(false);
            $table->datetime('escalated_at')->nullable();
            $table->integer('escalation_level')->default(0);
            $table->text('escalation_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes for reporting
            $table->index(['sla_due_time']);
            $table->index(['response_sla_met', 'resolution_sla_met']);
            $table->index(['was_escalated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_sla_tracking');
    }
};
