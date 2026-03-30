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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();

            // Sesuai ERD: menggunakan result_id dan diarahkan ke tabel user_results
            $table->foreignId('result_id')->constrained('user_results')->cascadeOnDelete();

            // Sesuai ERD: question_id
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();

            // Sesuai ERD: string selected_option (saya tambahkan nullable untuk jaga-jaga user tidak menjawab/kosong)
            $table->string('selected_option')->nullable();

            // Sesuai ERD: boolean is_correct
            $table->boolean('is_correct')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
