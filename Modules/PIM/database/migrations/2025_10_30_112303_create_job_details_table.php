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
        Schema::create('job_details', function (Blueprint $table) {
            $table->id();

            // Job Details Fields
            $table->string('job_title')->nullable();
            $table->string('job_category')->nullable();
            $table->string('job_unit')->nullable();
            $table->string('location')->nullable();

            // Date Fields
            $table->date('joining_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();

            // Status and Type Fields
            $table->string('employee_status')->default('active');
            $table->string('employee_type')->nullable(); // full_time, part_time, contract, intern
            $table->string('work_location')->nullable();
            $table->string('shift')->nullable(); // morning, afternoon, night, flexible

            // Text Fields
            $table->text('job_description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();

            // Foreign Keys
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('tenant_id');
            $table->string('activity_log')->nullable();

            // Reports To (Manager/Supervisor)
            $table->unsignedBigInteger('reports_to')->nullable();

            // Foreign Keys for relationships
            $table->foreignId('job_category_id')->nullable()
                ->constrained('job_categories')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('job_title_id')->nullable()
                ->constrained('job_titles')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('job_unit_id')->nullable()
                ->constrained('job_units')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();

            // Indexes for performance
            $table->index(['employee_id', 'tenant_id']);
            $table->index('job_category_id');
            $table->index('job_title_id');
            $table->index('job_unit_id');
            $table->index('reports_to');
            $table->index('employee_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_details');
    }
};
