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
            // Hapus index lama jika sudah ada (agar tidak duplicate error)
            try {
                $table->dropFullText('customer_search_index');
            } catch (\Exception $e) {}
            try {
                $table->dropFullText(['nopol']);
            } catch (\Exception $e) {}

            // Buat indeks baru
            $table->fullText('nopol');
            $table->fullText('nama');
            $table->fullText('norak');
            $table->fullText('nosin');
            $table->fullText(['nopol', 'nama', 'norak', 'nosin'], 'customer_search_index');
        });
    }

    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            $table->dropFullText(['nopol']);
            $table->dropFullText(['nama']);
            $table->dropFullText(['norak']);
            $table->dropFullText(['nosin']);
            $table->dropFullText('customer_search_index');
        });
    }
};
