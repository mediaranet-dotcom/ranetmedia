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
            $table->foreignId('odp_id')->nullable()->constrained('odps')->onDelete('set null');
            $table->integer('odp_port')->nullable(); // Port number di ODP (1-8, 1-16, etc)
            $table->string('fiber_cable_color')->nullable(); // Warna kabel fiber
            $table->decimal('signal_strength', 5, 2)->nullable(); // Signal strength in dBm
            $table->text('installation_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['odp_id']);
            $table->dropColumn(['odp_id', 'odp_port', 'fiber_cable_color', 'signal_strength', 'installation_notes']);
        });
    }
};
