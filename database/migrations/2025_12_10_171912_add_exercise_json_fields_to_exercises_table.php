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
            // Add fields to match JSON structure
            $table->text('problem_statement')->nullable()->after('description_bn');
            $table->text('problem_statement_bn')->nullable()->after('problem_statement');
            $table->text('input_description')->nullable()->after('problem_statement_bn');
            $table->text('input_description_bn')->nullable()->after('input_description');
            $table->text('output_description')->nullable()->after('input_description_bn');
            $table->text('output_description_bn')->nullable()->after('output_description');
            $table->text('sample_input')->nullable()->after('output_description_bn');
            $table->text('sample_input_bn')->nullable()->after('sample_input');
            $table->text('sample_output')->nullable()->after('sample_input_bn');
            $table->text('sample_output_bn')->nullable()->after('sample_output');
            $table->string('language_id')->nullable()->after('programming_language');
            $table->string('language_name')->nullable()->after('language_id');
            $table->string('language_name_bn')->nullable()->after('language_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn([
                'problem_statement',
                'problem_statement_bn',
                'input_description',
                'input_description_bn',
                'output_description',
                'output_description_bn',
                'sample_input',
                'sample_input_bn',
                'sample_output',
                'sample_output_bn',
                'language_id',
                'language_name',
                'language_name_bn'
            ]);
        });
    }
};
