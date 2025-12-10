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
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('language_id'); // e.g., 'javascript', 'python', 'html'
            $table->string('title');
            $table->text('content');
            $table->text('code_example')->nullable();
            $table->integer('order')->default(0); // For ordering tutorials
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            
            // Index for faster queries
            $table->index('language_id');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorials');
    }
};
