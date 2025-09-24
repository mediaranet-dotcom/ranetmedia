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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->nullable()->constrained('ticket_comments')->onDelete('cascade');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('original_name');
            $table->string('file_name'); // Stored file name
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // in bytes
            $table->string('file_hash')->nullable(); // For duplicate detection
            
            $table->enum('type', ['image', 'document', 'video', 'audio', 'other'])->default('other');
            $table->boolean('is_public')->default(true); // Visible to customer
            $table->text('description')->nullable();
            
            // Image specific fields
            $table->integer('image_width')->nullable();
            $table->integer('image_height')->nullable();
            $table->string('thumbnail_path')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['ticket_id', 'type']);
            $table->index(['file_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
