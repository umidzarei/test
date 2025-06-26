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
        Schema::create('calculated_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RequestEmployee::class)->constrained()->onDelete('cascade');
            $table->decimal('total_hq_score:', 8, 3)->nullable();
            $table->decimal('diabetes_risk_score', 8, 3)->nullable();
            $table->decimal('metabolic_syndrome_score', 8, 3)->nullable();
            $table->decimal('cvd_risk_score', 8, 3)->nullable();
            $table->decimal('fatty_liver_disease_risk_score', 8, 3)->nullable();
            $table->decimal('depression_score:', 8, 3)->nullable();
            $table->decimal('anxiety_score:', 8, 3)->nullable();
            $table->decimal('stress_score:', 8, 3)->nullable();
            $table->decimal('nutrition_score:', 8, 3)->nullable();
            $table->decimal('physical_activity_score:', 8, 3)->nullable();
            $table->decimal('sleep_health_score:', 8, 3)->nullable();
            $table->decimal('habits_health_engagement_score:', 8, 3)->nullable();
            $table->decimal('stress_management_wellbeing_score', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculated_scores');
    }
};
