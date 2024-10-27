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
        Schema::create('fuel_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('management_project_id')->index();
            $table->unsignedBigInteger('asset_id')->index();
            $table->text('receiver');
            $table->date('date')->default(date('Y-m-d'));
            $table->integer('liter');
            $table->text('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_consumptions');
    }
};
