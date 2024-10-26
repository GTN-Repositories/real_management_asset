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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->text('image')->nullable();
            $table->text('name');
            $table->text('manager');
            $table->text('category');
            $table->integer('cost')->nullable();
            $table->longText('description')->nullable();
            $table->text('serial_number')->nullable();
            $table->text('model_number')->nullable();
            $table->integer('warranty_period')->nullable();
            $table->text('assets_location')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('depreciation')->nullable();
            $table->text('depreciation_percentage')->nullable();
            $table->text('depreciation_method')->nullable();
            $table->integer('residual_value')->nullable();
            $table->integer('appreciation_rate')->nullable();
            $table->integer('appreciation_period')->nullable();
            $table->text('supplier_name')->nullable();
            $table->text('supplier_phone_number')->nullable();
            $table->text('supplier_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
