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
            $table->dropColumn('amount');
            $table->decimal('bailout_amount', 15, 2)->nullable();
            $table->decimal('finance_payout', 15, 2)->nullable();
            $table->date('finance_deadline')->nullable();
            $table->decimal('handling_fee', 15, 2)->nullable();
            $table->decimal('vendor_fee', 15, 2)->nullable();
            $table->boolean('is_finance_paid')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_data', function (Blueprint $table) {
            //
        });
    }
};
