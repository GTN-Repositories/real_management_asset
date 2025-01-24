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
            $table->datetime('estimate_finish')->nullable();
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->datetime('estimate_finish')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
