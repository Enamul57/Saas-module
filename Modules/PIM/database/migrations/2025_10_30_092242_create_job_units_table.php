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
        Schema::create('job_units', function (Blueprint $table) {
            $table->id();
            $table->string('job_unit_name');
            $table->foreignId('job_category_id')       // safer than unsignedBigInteger + foreign
                ->constrained('job_categories')
                ->nullOnDelete()                     // column must allow NULL
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_units');
    }
};
