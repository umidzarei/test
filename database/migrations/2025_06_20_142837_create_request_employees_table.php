<?php

use App\Models\Employee;
use App\Models\Request;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Request::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Employee::class)->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'in_process', 'done', 'reject'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_employees');
    }
};
