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
        Schema::table('management_projects', function (Blueprint $table) {
            $table->json('employee_id')->after('asset_id')->nullable();
            $table->integer('value_project')->after('employee_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management_projects', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'value_project']);
        });
    }
};
