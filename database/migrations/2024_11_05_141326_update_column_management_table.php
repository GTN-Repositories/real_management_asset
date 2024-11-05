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
        Schema::table('management_projects', function (Blueprint $table) {
            DB::statement('ALTER TABLE management_projects ADD asset_id_json JSON NULL AFTER asset_id');
            DB::statement('UPDATE management_projects SET asset_id_json = CAST(asset_id AS JSON)');
            DB::statement('ALTER TABLE management_projects DROP COLUMN asset_id');
            DB::statement('ALTER TABLE management_projects CHANGE COLUMN asset_id_json asset_id JSON NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management_projects', function (Blueprint $table) {
            DB::statement('ALTER TABLE management_projects ADD asset_idUnsigned BIGINT UNSIGNED NULL AFTER asset_id');
            DB::statement('UPDATE management_projects SET asset_idUnsigned = CAST(asset_id AS UNSIGNED)');
            DB::statement('ALTER TABLE management_projects DROP COLUMN asset_id');
            DB::statement('ALTER TABLE management_projects CHANGE COLUMN asset_idUnsigned asset_id BIGINT UNSIGNED NULL');
        });
    }
};
