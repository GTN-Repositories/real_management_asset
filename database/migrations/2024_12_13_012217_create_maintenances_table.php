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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('workshop')->nullable();
            $table->unsignedBigInteger('inspection_schedule_id')->index();
            $table->json('employee_id');
            $table->integer('status')->defaut(1);

            // IF ON HOLD
            $table->string('code_delay')->nullable();
            $table->string('delay_reason')->nullable();
            $table->date('estimate_finish')->nullable();
            $table->integer('delay_hours')->nullable();
            $table->timestamp('start_maintenace')->nullable();
            $table->timestamp('end_maintenace')->nullable();
            $table->timestamp('deviasi')->nullable();

            // IF FINISH
            $table->timestamp('finish_at')->nullable();
            $table->string('hm')->nullable();
            $table->string('km')->nullable();
            $table->string('location')->nullable();
            $table->text('detail_problem')->nullable();
            $table->string('action_to_do')->nullable();
            $table->string('urgention')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
