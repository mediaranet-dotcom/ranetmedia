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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // TKT-2025-0001
            $table->string('title');
            $table->longText('description');
            
            // Relationships
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->constrained('ticket_categories');
            $table->foreignId('priority_id')->constrained('ticket_priorities');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            
            // Status and tracking
            $table->enum('status', [
                'open', 'in_progress', 'pending_customer', 'pending_vendor', 
                'resolved', 'closed', 'cancelled'
            ])->default('open');
            
            // Contact information
            $table->string('contact_method')->default('email'); // email, phone, whatsapp, in_person
            $table->string('contact_value')->nullable(); // email address or phone number used
            
            // Technical details
            $table->json('technical_details')->nullable(); // Store technical info as JSON
            $table->string('location')->nullable(); // Customer location for field visits
            $table->boolean('requires_field_visit')->default(false);
            $table->datetime('scheduled_visit_at')->nullable();
            
            // SLA tracking
            $table->datetime('sla_due_at')->nullable(); // When response is due
            $table->datetime('escalation_due_at')->nullable(); // When to escalate
            $table->boolean('is_escalated')->default(false);
            $table->integer('escalation_level')->default(0); // 0=none, 1=supervisor, 2=manager, 3=director
            
            // Resolution tracking
            $table->datetime('first_response_at')->nullable();
            $table->datetime('resolved_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('customer_satisfaction_rating')->nullable(); // 1-5 stars
            $table->text('customer_feedback')->nullable();
            
            // Billing and costs
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->boolean('is_billable')->default(false);
            $table->boolean('is_warranty')->default(false);
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->integer('total_comments')->default(0);
            $table->integer('total_attachments')->default(0);
            $table->datetime('last_activity_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['priority_id', 'status']);
            $table->index(['sla_due_at']);
            $table->index(['escalation_due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
