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
            $table->string('asset_number')->nullable()->after('id');
            $table->string('unit')->nullable()->after('asset_number');
            $table->string('license_plate')->nullable()->after('unit');
            $table->string('classification')->nullable()->after('license_plate');
            $table->string('machine_number')->nullable()->after('classification');
            $table->string('nik')->nullable()->after('machine_number');
            $table->string('color')->nullable()->after('nik');
            $table->enum(
                'status',
                ['Idle', 'StandBy', 'OnHold', 'Finish', 'Damaged', 'Fair', 'UnderMaintenance', 'Active', 'Scheduled', 'InProgress', 'NeedsRepair', 'Good']
            )->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('no_asset');
            $table->dropColumn('unit');
            $table->dropColumn('no_polisi');
            $table->dropColumn('klasifikasi');
            $table->dropColumn('no_mesin');
            $table->dropColumn('nik');
            $table->dropColumn('warna');
            $table->dropColumn('status');
        });
    }
};
