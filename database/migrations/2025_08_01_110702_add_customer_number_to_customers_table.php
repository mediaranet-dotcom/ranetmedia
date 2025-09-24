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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_number')->nullable()->unique()->after('id');
            $table->string('province')->nullable()->after('address');
            $table->string('regency')->nullable()->after('province');
            $table->string('district')->nullable()->after('regency');
            $table->string('village')->nullable()->after('district');
            $table->string('hamlet')->nullable()->after('village');
            $table->string('rt')->nullable()->after('hamlet');
            $table->string('rw')->nullable()->after('rt');
            $table->string('postal_code')->nullable()->after('rw');
            $table->text('address_notes')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'customer_number',
                'province',
                'regency',
                'district',
                'village',
                'hamlet',
                'rt',
                'rw',
                'postal_code',
                'address_notes'
            ]);
        });
    }
};
