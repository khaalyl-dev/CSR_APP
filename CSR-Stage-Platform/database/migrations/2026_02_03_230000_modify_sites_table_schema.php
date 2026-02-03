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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->renameColumn('id', 'site_id');
            $table->renameColumn('name', 'site_name');
            $table->string('location')->nullable()->after('site_name');
            $table->string('manager')->nullable()->after('location');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('site_id')->references('site_id')->on('sites')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->renameColumn('site_id', 'id');
            $table->renameColumn('site_name', 'name');
            $table->dropColumn(['location', 'manager']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('site_id')->references('id')->on('sites')->nullOnDelete();
        });
    }
};
