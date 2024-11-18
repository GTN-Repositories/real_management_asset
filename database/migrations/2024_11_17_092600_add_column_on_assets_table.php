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
            $table->string('stnk')->nullable();
            $table->date('stnk_date')->nullable();
            $table->string('asuransi')->nullable();
            $table->date('asuransi_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('stnk');
            $table->dropColumn('stnk_date');
            $table->dropColumn('asuransi');
            $table->dropColumn('asuransi_date');
        });
    }
};
