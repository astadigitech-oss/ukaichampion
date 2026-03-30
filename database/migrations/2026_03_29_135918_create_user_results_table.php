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
        Schema::create('user_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_package_id')->constrained()->cascadeOnDelete();

            $table->integer('attempt_number'); // Mencatat ini ujian ke-1, ke-2, dst.
            $table->decimal('score', 5, 2)->nullable(); // Skor dari 0 sampai 100
            $table->timestamp('finished_at')->nullable(); // Kapan ujian disubmit

            $table->timestamps();
            // Tidak perlu softDeletes di sini agar performa penghitungan nilai lebih cepat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_results');
    }
};
