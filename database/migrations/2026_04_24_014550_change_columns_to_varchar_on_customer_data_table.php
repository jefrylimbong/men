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
        Schema::table('customer_data', function (Blueprint $table) {
            $table->string('tenor')->nullable()->change();
            $table->string('ke')->nullable()->change();
            $table->string('od')->nullable()->change();
            $table->string('ph')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            $table->integer('tenor')->nullable()->change();
            $table->integer('ke')->nullable()->change();
            $table->integer('od')->nullable()->change();
            $table->decimal('ph', 15, 2)->nullable()->change();
        });
    }
};
