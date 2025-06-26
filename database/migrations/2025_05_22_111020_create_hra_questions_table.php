<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hra_questions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('text');
            $table->enum('input_type', ['number', 'single_choice', 'multi_choice', 'scale_0_3']);
            $table->string('section')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hra_questions');
    }
};

