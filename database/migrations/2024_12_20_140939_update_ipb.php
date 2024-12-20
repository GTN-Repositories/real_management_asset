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
        //
        Schema::table('ipbs', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
            $table->string('issued_liter')->nullable()->change();
            $table->string('usage_liter')->nullable()->change();
            $table->string('balance')->nullable()->change();
            $table->string('unit_price')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->unsignedBigInteger('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
