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
        Schema::table('withdrawal_data', function (Blueprint $table) {
            $table->boolean('is_vendor_paid')->default(false)->after('is_finance_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_data', function (Blueprint $table) {
            $table->dropColumn('is_vendor_paid');
        });
    }
};
