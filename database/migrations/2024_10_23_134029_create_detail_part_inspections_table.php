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
        Schema::create('detail_part_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_question_id')->index();
            $table->unsignedBigInteger('item_id')->index();
            $table->decimal('price', 15, 2);
            $table->integer('quantity');
            $table->string('priority_scale');
            $table->date('request_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_part_inspections');
    }
};