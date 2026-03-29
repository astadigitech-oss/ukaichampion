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
        Schema::create('exam_packages', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel exam_categories
            $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete();

            $table->string('title'); // Contoh: "Dasar Laravel"
            $table->integer('time_limit'); // Durasi ujian dalam satuan menit

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_packages');
    }
};
