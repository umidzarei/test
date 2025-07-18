<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hra_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('hra_questions')->onDelete('cascade');
            $table->string('value');
            $table->string('label');
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hra_question_options');
    }
};
