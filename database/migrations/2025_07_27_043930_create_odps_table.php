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
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ODP-001, ODP-002, etc
            $table->string('code')->unique(); // Kode unik ODP
            $table->text('description')->nullable();

            // Location Information
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable(); // GPS coordinates
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('area')->nullable(); // Area/Kelurahan
            $table->string('district')->nullable(); // Kecamatan

            // Technical Specifications
            $table->integer('total_ports')->default(8); // Total port di ODP
            $table->integer('used_ports')->default(0); // Port yang sudah digunakan
            $table->integer('available_ports')->default(8); // Port yang tersedia
            $table->string('odp_type')->default('8_port'); // 8_port, 16_port, 32_port
            $table->string('manufacturer')->nullable(); // Vendor ODP
            $table->string('model')->nullable(); // Model ODP

            // Network Information
            $table->string('feeder_cable')->nullable(); // Kabel feeder yang terhubung
            $table->integer('fiber_count')->nullable(); // Jumlah fiber di kabel feeder
            $table->string('splitter_ratio')->nullable(); // 1:8, 1:16, 1:32

            // Status & Condition
            $table->enum('status', ['active', 'inactive', 'maintenance', 'damaged'])->default('active');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('good');
            $table->date('installation_date')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};
