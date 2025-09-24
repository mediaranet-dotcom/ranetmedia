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
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('author_name')->nullable(); // For customer comments
            $table->string('author_email')->nullable(); // For customer comments
            $table->enum('author_type', ['staff', 'customer', 'system'])->default('staff');
            
            $table->longText('content');
            $table->enum('type', [
                'comment', 'status_change', 'assignment_change', 'priority_change',
                'category_change', 'resolution', 'escalation', 'system_note'
            ])->default('comment');
            
            // Visibility and notifications
            $table->boolean('is_internal')->default(false); // Internal staff notes
            $table->boolean('is_public')->default(true); // Visible to customer
            $table->boolean('notify_customer')->default(true);
            $table->boolean('notify_assigned_staff')->default(true);
            
            // Change tracking (for status changes, etc.)
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            
            // Time tracking
            $table->integer('time_spent_minutes')->nullable(); // Time spent on this update
            $table->boolean('is_billable_time')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index(['author_type', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
