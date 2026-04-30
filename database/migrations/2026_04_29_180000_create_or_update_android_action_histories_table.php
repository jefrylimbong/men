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
        // Jika tabel belum ada (karena tidak ada di list migrasi), kita buat
        if (! Schema::hasTable('android_action_histories')) {
            Schema::create('android_action_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name')->nullable();
                $table->string('action');
                $table->text('description')->nullable();
                $table->integer('duration_seconds')->default(0);
                $table->timestamps();
            });
        } else {
            // Jika sudah ada, kita tambahkan kolom yang kurang
            Schema::table('android_action_histories', function (Blueprint $table) {
                if (! Schema::hasColumn('android_action_histories', 'user_id')) {
                    $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
                }
                if (! Schema::hasColumn('android_action_histories', 'duration_seconds')) {
                    $table->integer('duration_seconds')->after('description')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('android_action_histories');
    }
};
