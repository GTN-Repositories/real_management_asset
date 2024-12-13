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
        Schema::create('costum_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id')->index()->nullable();
            $table->string('nama_field')->nullable();
            $table->text('nilai_field')->nullable();
            $table->string('tipe_field')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costum_fields');
    }
};
