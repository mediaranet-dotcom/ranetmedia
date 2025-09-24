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
        Schema::table('packages', function (Blueprint $table) {
            $table->enum('technology_type', ['fiber', 'wireless', 'hybrid'])
                ->default('fiber')
                ->after('description')
                ->comment('Technology type: fiber (ODP required), wireless (no ODP), hybrid (both)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('technology_type');
        });
    }
};
