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
        Schema::table('ipbs', function (Blueprint $table) {
            $table->integer('usage_liter')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('employee_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ipbs', function (Blueprint $table) {
            $table->integer('usage_liter')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->dropColumn('employee_id');
        });
    }
};
