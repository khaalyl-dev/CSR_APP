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
        Schema::create('annual_plans', function (Blueprint $table) {
            $table->id('plan_id');
            $table->unsignedBigInteger('site_id');
            $table->year('year');
            $table->enum('category', ['Environnement', 'Social', 'Gouvernance']);
            $table->string('activity_type');
            $table->text('description');
            $table->enum('status', ['draft', 'validated'])->default('draft');
            $table->timestamps();

            $table->foreign('site_id')->references('site_id')->on('sites')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_plans');
    }
};
