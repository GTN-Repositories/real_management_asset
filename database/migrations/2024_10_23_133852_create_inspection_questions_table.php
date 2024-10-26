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
        Schema::create('inspection_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_schedule_id')->index();
            $table->unsignedBigInteger('form_id')->index();
            $table->string('answer')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_questions');
    }
};
