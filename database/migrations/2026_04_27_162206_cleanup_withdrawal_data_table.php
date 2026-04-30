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
            $table->dropForeign(['finance_master_id']);
            $table->dropColumn([
                'customer_name',
                'finance_master_id',
                'plate_number',
                'engine_number',
                'frame_number',
                'vehicle_type',
            ]);
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
