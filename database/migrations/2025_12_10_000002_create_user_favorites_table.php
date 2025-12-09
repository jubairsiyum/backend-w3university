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
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['course', 'tutorial', 'blog', 'tool', 'resource']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('category')->nullable(); // 'JavaScript', 'Python', 'Web Development', etc
            $table->json('tags')->nullable(); // ['frontend', 'react', 'tutorial']
            $table->integer('order')->default(0); // For custom ordering
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};
