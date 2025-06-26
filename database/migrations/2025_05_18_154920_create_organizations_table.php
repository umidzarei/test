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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('national_id')->nullable();
            $table->string('reg_number')->nullable();
            $table->string('economic_code')->nullable();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('representative_name')->nullable();
            $table->string('representative_position')->nullable();
            $table->string('representative_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
