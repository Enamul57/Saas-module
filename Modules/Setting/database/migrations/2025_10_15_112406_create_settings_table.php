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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // Key for the setting (unique)
            $table->string('key')->unique();

            // Value (text to store anything, can store JSON as string)
            $table->text('value')->nullable();

            // Type of the value (string, boolean, number, json)
            $table->string('type')->default('string');

            // Group/category of the setting (organization, leave, payroll, etc.)
            $table->string('group')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unique(['key', 'tenant_id']);
            // Optional description for admin clarity
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
