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
        Schema::create('loadsheets', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('management_project_id')->index();
            $table->unsignedBigInteger('asset_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->integer('hours');
            $table->string('type')->nullable();
            $table->text('location')->nullable();
            $table->unsignedBigInteger('soil_type_id')->index()->nullable();
            $table->integer('bpit')->nullable();
            $table->integer('kilometer')->nullable();
            $table->integer('loadsheet')->nullable();
            $table->integer('perload')->nullable();
            $table->integer('lose_factor')->nullable();
            $table->string('cubication')->nullable();
            $table->integer('price')->nullable();
            $table->string('billing_status')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loadsheets');
    }
};
