<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('prerequisite_course_id')->constrained('courses')->cascadeOnDelete();

            $table->primary(['course_id', 'prerequisite_course_id']);
        });

        // Mot hoc phan khong the la tien quyet cua chinh no. SQLite khong cho them
        // CHECK sau khi tao bang; quy tac nay con duoc Service kiem tra (phat hien vong lap).
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE course_prerequisites ADD CONSTRAINT chk_course_prerequisites_not_self '
                .'CHECK (course_id <> prerequisite_course_id)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
