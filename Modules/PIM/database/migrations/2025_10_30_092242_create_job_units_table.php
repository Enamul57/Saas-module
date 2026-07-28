<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_units', function (Blueprint $table) {
            $table->id();
            $table->string('job_unit_name');
            $table->unsignedBigInteger('job_category_id')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();

            // Add indexes (no foreign keys)
            $table->index('job_category_id');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_units');
    }
};
