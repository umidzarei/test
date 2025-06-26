<?php

use App\Models\RequestEmployee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hra_questionnaire_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RequestEmployee::class)->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hra_questionnaire_instances');
    }
};
