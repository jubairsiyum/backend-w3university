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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            
            // Core Bilingual Content
            $table->string('title');
            $table->string('title_bn');
            $table->text('excerpt');
            $table->text('excerpt_bn');
            $table->longText('content');
            $table->longText('content_bn');
            
            // Author Information
            $table->string('author');
            $table->string('author_bn');
            
            // Categorization
            $table->string('category');
            $table->string('category_bn');
            $table->json('tags')->nullable();
            $table->json('tags_bn')->nullable();
            
            // Metadata
            $table->string('read_time')->nullable();
            $table->string('read_time_bn')->nullable();
            $table->string('image_url')->nullable();
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->unsignedBigInteger('views')->default(0);
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('slug');
            $table->index('status');
            $table->index('category');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
