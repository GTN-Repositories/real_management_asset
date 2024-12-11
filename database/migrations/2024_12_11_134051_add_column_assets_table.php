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
        Schema::table('assets', function (Blueprint $table) {
            $table->enum(
                'status',
                ['Idle', 'StandBy', 'OnHold', 'Finish', 'Damaged', 'Fair', 'UnderMaintenance', 'Active', 'Scheduled', 'InProgress', 'NeedsRepair', 'Good', 'Overdue']
            )->nullable()->after('color')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
        });
    }
};
