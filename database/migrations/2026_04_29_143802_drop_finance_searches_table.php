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
        Schema::dropIfExists('finance_searches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('finance_searches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('nopol')->nullable();
            $table->string('finance')->nullable();
            $table->timestamps();
        });
    }
};
