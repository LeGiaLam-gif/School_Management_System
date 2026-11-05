<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ket qua tong ket da tinh. Tinh lai duoc khi doi diem; cong bo co kiem soat qua published_at/locked_at.
        Schema::create('final_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('total_score', 5, 2);          // thang 10
            $table->string('letter_grade', 2);
            $table->decimal('gpa_point', 3, 2);            // thang 4
            $table->boolean('is_passed');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('unlock_reason')->nullable();
            $table->timestamps();

            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_results');
    }
};
