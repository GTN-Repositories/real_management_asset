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
        Schema::table('fuel_consumptions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('receiver')->index();
        });

        // DB::table('fuel_consumptions')->get()->each(function ($item) {
        //     DB::table('fuel_consumptions')->where('id', $item->id)->update(['user_id' => $item->receiver]);
        // });

        Schema::table('fuel_consumptions', function (Blueprint $table) {
            $table->dropColumn('receiver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_consumptions', function (Blueprint $table) {
            $table->text('receiver')->after('user_id');
        });

        DB::table('fuel_consumptions')->get()->each(function ($item) {
            DB::table('fuel_consumptions')->where('id', $item->id)->update(['receiver' => $item->user_id]);
        });

        Schema::table('fuel_consumptions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
