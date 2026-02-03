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
        Schema::create('realized_activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('site_id');
            $table->string('activity_name');
            $table->enum('category', ['Environnement', 'Social', 'Gouvernance']);
            $table->string('activity_type');
            $table->decimal('cost', 12, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed'])->default('pending');
            $table->date('performed_at')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('plan_id')->on('annual_plans')->nullOnDelete();
            $table->foreign('site_id')->references('site_id')->on('sites')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realized_activities');
    }
};
