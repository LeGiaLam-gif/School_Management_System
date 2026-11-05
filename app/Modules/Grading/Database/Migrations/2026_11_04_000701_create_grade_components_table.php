<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cau hinh cot diem cua lop (chuyen can, giua ky, cuoi ky...) kem trong so; tong = 100 (BR-07).
        Schema::create('grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('weight', 5, 2);
            $table->decimal('max_score', 5, 2)->default(10);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['class_section_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_components');
    }
};
