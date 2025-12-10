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
        Schema::table('exercises', function (Blueprint $table) {
            // Make bilingual fields nullable
            $table->string('title_bn')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->text('description_bn')->nullable()->change();
            $table->longText('instructions')->nullable()->change();
            $table->longText('instructions_bn')->nullable()->change();
            $table->string('difficulty_bn')->nullable()->change();
            $table->string('category')->nullable()->change();
            $table->string('category_bn')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->string('title_bn')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->text('description_bn')->nullable(false)->change();
            $table->longText('instructions')->nullable(false)->change();
            $table->longText('instructions_bn')->nullable(false)->change();
            $table->string('difficulty_bn')->nullable(false)->change();
            $table->string('category')->nullable(false)->change();
            $table->string('category_bn')->nullable(false)->change();
        });
    }
};
