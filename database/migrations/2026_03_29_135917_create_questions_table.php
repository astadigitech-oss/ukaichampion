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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel exam_packages
            $table->foreignId('exam_package_id')->constrained()->cascadeOnDelete();

            $table->text('question_text'); // Teks pertanyaan
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d')->nullable(); // Dibuat nullable jika soal hanya sampai c
            $table->string('option_e')->nullable(); // Dibuat nullable jika soal hanya sampai D

            $table->char('correct_answer', 1); // Kunci jawaban (A/B/C/D/E)
            $table->text('explanation')->nullable(); // Teks pembahasan soal

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
