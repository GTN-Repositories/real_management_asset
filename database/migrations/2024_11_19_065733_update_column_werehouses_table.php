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
        Schema::table('werehouses', function (Blueprint $table) {
            $table->dropColumn('site_id');
            $table->string('location')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('werehouses', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->index()->after('name');
            $table->dropColumn('location');
        });
    }
};
