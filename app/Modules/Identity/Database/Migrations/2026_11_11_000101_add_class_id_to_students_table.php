<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')
                ->nullable()
                ->after('program_id')
                ->constrained('administrative_classes')
                ->nullOnDelete();

            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropIndex(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
