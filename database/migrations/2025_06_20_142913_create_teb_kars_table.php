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
        Schema::create('teb_kars', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RequestEmployee::class)->constrained()->onDelete('cascade');
            $table->decimal('height', 8, 3)->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->decimal('waist_circumference', 8, 3)->nullable();
            $table->decimal('SBP', 8, 3)->nullable();
            $table->decimal('DBP', 8, 3)->nullable();
            $table->decimal('BMI', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teb_kars');
    }
};
