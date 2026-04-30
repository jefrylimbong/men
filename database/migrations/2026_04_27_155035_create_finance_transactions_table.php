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
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('category'); // Penarikan, Operasional, Fee, Payment, etc.
            $table->decimal('amount', 15, 2);

            // Debit Entity (Penerima uang / Piutang bertambah)
            $table->string('debit_type'); // PT, Vendor, Finance
            $table->unsignedBigInteger('debit_id')->nullable();

            // Credit Entity (Pemberi uang / Hutang bertambah)
            $table->string('credit_type'); // PT, Vendor, Finance
            $table->unsignedBigInteger('credit_id')->nullable();

            $table->unsignedBigInteger('reference_id')->nullable(); // Link to WithdrawalData or other
            $table->string('description')->nullable();
            $table->string('status')->default('completed'); // pending, completed, canceled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
