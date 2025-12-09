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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('activity_date');
            $table->integer('minutes_active')->default(0); // Active time in minutes for that day
            $table->integer('lessons_completed')->default(0);
            $table->integer('exercises_completed')->default(0);
            $table->integer('quizzes_completed')->default(0);
            $table->integer('blogs_read')->default(0);
            $table->integer('comments_posted')->default(0);
            $table->integer('code_snippets_created')->default(0);
            $table->integer('streak_days')->default(0); // Current streak
            $table->timestamps();
            
            $table->unique(['user_id', 'activity_date']);
            $table->index('activity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
