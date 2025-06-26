<?php

use App\Models\OccupationalMedicine;
use App\Models\Organization;
use App\Models\Physician;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracking_code')->nullable();
            $table->foreignIdFor(Organization::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Physician::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(OccupationalMedicine::class)->nullable()->constrained()->onDelete('cascade');
            $table->morphs('requester');
            $table->enum('status', ['pending', 'in_process', 'done', 'reject'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
