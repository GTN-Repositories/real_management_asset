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
            $table->unsignedBigInteger('manager')->index()->nullable()->change();
            $table->unsignedBigInteger('category')->index()->nullable()->change();
            $table->unsignedBigInteger('assets_location')->index()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('manager')->nullable()->change();
            $table->unsignedBigInteger('category')->nullable()->change();
            $table->unsignedBigInteger('assets_location')->nullable()->change();
        });
    }
};

