<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inspection_schedules', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'in_progress', 'on_hold', 'finish'])->default('scheduled')->after('asset_kanibal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
