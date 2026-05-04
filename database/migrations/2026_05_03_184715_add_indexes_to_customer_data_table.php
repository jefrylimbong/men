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
        // Hapus indeks jika sudah ada (manual via SQL agar aman)
        try {
            \DB::statement('ALTER TABLE customer_data DROP INDEX customer_search_index');
        } catch (\Exception $e) {}

        try {
            \DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nopol_fulltext');
        } catch (\Exception $e) {}

        Schema::table('customer_data', function (Blueprint $table) {
            // Buat ulang semua indeks
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
