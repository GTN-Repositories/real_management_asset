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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('police_number');
            $table->string('old_police_number')->nullable();
            $table->string('frame_number')->nullable();
            $table->string('merk')->nullable();
            $table->string('type_vehicle')->nullable();
            $table->string('type')->nullable();
            $table->integer('year')->default(date('Y'));
            $table->string('color')->default('white');
            $table->string('physical_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
