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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('minutes_active')->default(0);
            $table->integer('lessons_completed')->default(0);
            $table->integer('exercises_completed')->default(0);
            $table->integer('quizzes_completed')->default(0);
            $table->integer('blogs_read')->default(0);
            $table->integer('comments_posted')->default(0);
            $table->integer('code_snippets_created')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
