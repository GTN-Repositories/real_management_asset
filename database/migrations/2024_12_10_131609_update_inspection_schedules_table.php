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
        Schema::table('inspection_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('management_project_id')->index()->after('asset_id')->nullable();
            $table->string('workshop')->nullable();
            $table->string('mechanic_name')->nullable();
            $table->string('status')->default('UnderMaintenance')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'management_project_id',
                'workshop',
                'mechanic_name',
            ]);
        });
    }
};
