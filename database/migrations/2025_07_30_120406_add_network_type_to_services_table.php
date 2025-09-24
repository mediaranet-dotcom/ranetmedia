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
            $table->enum('network_type', ['odp', 'wireless', 'htb'])
                ->default('odp')
                ->after('package_id')
                ->comment('Network infrastructure type: odp (fiber), wireless (radio), htb (hotspot)');

            // Add fields for wireless/HTB
            $table->string('wireless_equipment')->nullable()->after('signal_strength');
            $table->string('antenna_type')->nullable()->after('wireless_equipment');
            $table->decimal('frequency', 8, 2)->nullable()->after('antenna_type');
            $table->string('htb_server')->nullable()->after('frequency');
            $table->string('access_point')->nullable()->after('htb_server');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'network_type',
                'wireless_equipment',
                'antenna_type',
                'frequency',
                'htb_server',
                'access_point'
            ]);
        });
    }
};
