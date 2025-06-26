<?php

use App\Models\RequestEmployee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lab_data', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RequestEmployee::class)->constrained()->onDelete('cascade');
            $table->decimal('FBS', 8, 3)->nullable();
            $table->decimal('total_cholesterol', 8, 3)->nullable();
            $table->decimal('HDL_cholesterol', 8, 3)->nullable();
            $table->decimal('triglycerides', 8, 3)->nullable();
            $table->decimal('ALT', 8, 3)->nullable();
            $table->decimal('AST', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_data');
    }
};
