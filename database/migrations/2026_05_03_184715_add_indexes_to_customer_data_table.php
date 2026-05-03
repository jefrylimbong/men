<?php
/**
 * Migration untuk menambah index pada kolom pencarian customer_data
 * Jalankan: php artisan migrate
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            $table->index('nopol');
            $table->index('nama');
            $table->index('norak');
            $table->index('nosin');
        });
    }

    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            $table->dropIndex(['nopol']);
            $table->dropIndex(['nama']);
            $table->dropIndex(['norak']);
            $table->dropIndex(['nosin']);
        });
    }
};
