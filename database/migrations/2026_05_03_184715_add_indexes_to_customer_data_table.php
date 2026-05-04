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
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_search_index'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_data DROP INDEX customer_data_nopol_fulltext'); } catch (\Exception $e) {}

        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_nopol_fulltext`(`nopol`)'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_nama_fulltext`(`nama`)'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_norak_fulltext`(`norak`)'); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `customer_data` ADD FULLTEXT `customer_data_nosin_fulltext`(`nosin`)'); } catch (\Exception $e) {}
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
