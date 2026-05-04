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
            DB::statement('ALTER TABLE customer_data DROP INDEX customer_search_index');
        } catch (Exception $e) {
        }

        try {
            DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nopol_fulltext');
        } catch (Exception $e) {
        }

        Schema::table('customer_data', function (Blueprint $table) {
            try {
                $table->fullText('nopol');
            } catch (Exception $e) {
            }
            try {
                $table->fullText('nama');
            } catch (Exception $e) {
            }
            try {
                $table->fullText('norak');
            } catch (Exception $e) {
            }
            try {
                $table->fullText('nosin');
            } catch (Exception $e) {
            }
            try {
                $table->fullText(['nopol', 'nama', 'norak', 'nosin'], 'customer_search_index');
            } catch (Exception $e) {
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            try {
                $table->dropFullText(['nopol']);
            } catch (Exception $e) {
            }
            try {
                $table->dropFullText(['nama']);
            } catch (Exception $e) {
            }
            try {
                $table->dropFullText(['norak']);
            } catch (Exception $e) {
            }
            try {
                $table->dropFullText(['nosin']);
            } catch (Exception $e) {
            }
            try {
                $table->dropFullText('customer_search_index');
            } catch (Exception $e) {
            }
        });
    }
};
