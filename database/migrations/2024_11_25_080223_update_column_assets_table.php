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
            $table->string('manager')->index()->nullable()->change();
            $table->string('category')->index()->nullable()->change();
            $table->string('assets_location')->index()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('manager')->nullable()->change();
            $table->string('category')->nullable()->change();
            $table->string('assets_location')->nullable()->change();
        });
    }
};

