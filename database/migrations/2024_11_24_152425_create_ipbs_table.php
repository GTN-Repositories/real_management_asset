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
        Schema::create('ipbs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('management_project_id')->index();
            $table->string('issued_liter')->nullable();
            $table->string('usage_liter');
            $table->string('balance');
            $table->string('unit_price');
            $table->string('fuel_truck')->default('vendor');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipbs');
    }
};  
