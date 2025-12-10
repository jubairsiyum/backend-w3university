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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            
            // Core Bilingual Content
            $table->string('title');
            $table->string('title_bn');
            $table->text('description');
            $table->text('description_bn');
            $table->longText('instructions');
            $table->longText('instructions_bn');
            
            // Exercise Details
            $table->string('difficulty'); // easy, medium, hard
            $table->string('difficulty_bn');
            $table->integer('duration')->nullable(); // in minutes
            $table->string('duration_bn')->nullable();
            
            // Categorization
            $table->string('category');
            $table->string('category_bn');
            $table->json('tags')->nullable();
            $table->json('tags_bn')->nullable();
            
            // Code/Solution (if applicable)
            $table->longText('starter_code')->nullable();
            $table->longText('solution_code')->nullable();
            $table->string('programming_language')->nullable();
            
            // Metadata
            $table->string('image_url')->nullable();
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('completions')->default(0);
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('slug');
            $table->index('status');
            $table->index('category');
            $table->index('difficulty');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
