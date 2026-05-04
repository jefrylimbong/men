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
        // Hapus indeks lama agar bisa dibuat ulang dengan N-Gram
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_search_index'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nopol_fulltext'); } catch (\Exception $e) {}

        // Buat indeks Full-Text dengan N-Gram Parser khusus Nopol (agar bisa cari angka di tengah)
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_nopol_fulltext`(`nopol`) WITH PARSER ngram'); } catch (\Exception $e) {}
        
        // Indeks lainnya tetap standar
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_nama_fulltext`(`nama`)'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_norak_fulltext`(`norak`)'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_nosin_fulltext`(`nosin`)'); } catch (\Exception $e) {}
        
        // Indeks gabungan untuk pencarian luas
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_search_index`(`nopol`, `nama`, `norak`, `nosin`)'); } catch (\Exception $e) {}
    }

    public function down(): void
    {
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nopol_fulltext'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nama_fulltext'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_norak_fulltext'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nosin_fulltext'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_search_index'); } catch (\Exception $e) {}
    }
};
