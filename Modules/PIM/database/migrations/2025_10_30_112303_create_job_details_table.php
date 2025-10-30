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
            $table->date('joining_date');
            $table->string('job_title');
            $table->string('job_category');
            $table->string('job_unit');
            $table->string('location');
            $table->string('employee_status');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('tenant_id');
            $table->string('activity_log');

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
