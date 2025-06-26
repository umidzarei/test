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
        Schema::create('organization_employee_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_employee_id');
            $table->foreignId('department_id');
            $table->timestamps();

            $table->foreign('organization_employee_id', 'org_emp_dept_orgempid_fk')
                ->references('id')->on('organization_employees')->onDelete('cascade');

            $table->foreign('department_id', 'org_emp_dept_deptid_fk')
                ->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_employee_departments');
    }
};
