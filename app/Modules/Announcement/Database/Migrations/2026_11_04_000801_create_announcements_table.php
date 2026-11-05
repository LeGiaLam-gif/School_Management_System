<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            // null khi scope = all (toan truong).
            $table->foreignId('class_section_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('scope', 20)->default('all');   // AnnouncementScope
            $table->string('title');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable(); // hen gio dang
            $table->timestamps();

            $table->index(['scope', 'published_at']);
            $table->index('class_section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
