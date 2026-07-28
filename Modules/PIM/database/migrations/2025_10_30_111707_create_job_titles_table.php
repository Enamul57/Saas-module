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
        if (!Schema::hasTable('job_titles')) {
            Schema::create('job_titles', function (Blueprint $table) {
                $table->id();
                $table->string('job_title_name');
                $table->unsignedBigInteger('job_unit_id')->nullable();
                $table->unsignedBigInteger('tenant_id');
                $table->timestamps();

                // Only add indexes, NO foreign keys
                $table->index('job_unit_id');
                $table->index('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_titles');
    }
};
