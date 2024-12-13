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
            $table->string('type');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('management_project_id');
            $table->string('note')->nullable();
            $table->json('item_id')->nullable();
            $table->json('asset_kanibal_id')->nullable();
            $table->json('item_stock')->nullable();
            $table->json('kanibal_stock')->nullable();
            $table->date('date');
            $table->string('result');
            $table->unsignedBigInteger('employee_id');
            $table->date('date_breakdown')->nullable();
            $table->string('hm')->nullable();
            $table->string('km')->nullable();
            $table->string('detail_problem')->nullable();
            $table->string('action')->nullable();
            $table->string('major_minor')->nullable();
            $table->date('date_reminder')->nullable();
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
