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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('username')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('location')->nullable();
            $table->string('timezone')->default('UTC');
            $table->date('date_of_birth')->nullable();
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->json('programming_languages')->nullable(); // ['JavaScript', 'Python', etc]
            $table->json('interests')->nullable(); // ['Web Development', 'AI', etc]
            $table->integer('daily_goal_minutes')->default(60); // Daily learning goal in minutes
            $table->boolean('email_notifications')->default(true);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
