<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hra_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hra_questionnaire_instance_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('hra_questions')->onDelete('cascade');
            $table->string('selected_option')->nullable();
            $table->text('answer_text')->nullable();
            $table->float('score_raw')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hra_answers');
    }
};
