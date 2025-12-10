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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            
            // Social media URLs
            $table->string('github_url')->nullable()->after('avatar');
            $table->string('linkedin_url')->nullable()->after('github_url');
            $table->string('twitter_url')->nullable()->after('linkedin_url');
            $table->string('portfolio_url')->nullable()->after('twitter_url');
            
            // Location and timezone
            $table->string('location')->nullable()->after('portfolio_url');
            $table->string('timezone', 50)->nullable()->after('location');
            
            // Personal info
            $table->date('date_of_birth')->nullable()->after('timezone');
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner')->after('date_of_birth');
            
            // Preferences stored as JSON
            $table->json('programming_languages')->nullable()->after('skill_level');
            $table->json('interests')->nullable()->after('programming_languages');
            $table->json('badges')->nullable()->after('interests');
            
            // Goals and settings
            $table->integer('daily_goal_minutes')->default(0)->after('badges');
            $table->boolean('email_notifications')->default(true)->after('daily_goal_minutes');
            $table->boolean('is_public')->default(true)->after('email_notifications');
            
            // Streaks
            $table->integer('current_streak')->default(0)->after('is_public');
            $table->integer('longest_streak')->default(0)->after('current_streak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'bio',
                'avatar',
                'github_url',
                'linkedin_url',
                'twitter_url',
                'portfolio_url',
                'location',
                'timezone',
                'date_of_birth',
                'skill_level',
                'programming_languages',
                'interests',
                'badges',
                'daily_goal_minutes',
                'email_notifications',
                'is_public',
                'current_streak',
                'longest_streak',
            ]);
        });
    }
};
