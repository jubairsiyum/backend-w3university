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
        Schema::create('user_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_courses_completed')->default(0);
            $table->integer('total_lessons_completed')->default(0);
            $table->integer('total_exercises_completed')->default(0);
            $table->integer('total_quizzes_completed')->default(0);
            $table->decimal('average_quiz_score', 5, 2)->default(0); // Percentage
            $table->integer('total_certificates_earned')->default(0);
            $table->integer('total_hours_learned')->default(0); // Total learning time in hours
            $table->integer('current_streak')->default(0); // Current learning streak in days
            $table->integer('longest_streak')->default(0); // Longest streak ever
            $table->integer('total_points')->default(0); // Gamification points
            $table->integer('experience_level')->default(1); // User level
            $table->json('badges')->nullable(); // Earned badges ['early-bird', 'week-warrior', etc]
            $table->json('skills_completed')->nullable(); // Completed skills with details
            $table->date('last_active_date')->nullable();
            $table->timestamp('joined_date')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_performance');
    }
};
