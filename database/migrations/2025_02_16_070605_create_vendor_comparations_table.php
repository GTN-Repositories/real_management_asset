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
        Schema::create('vendor_comparations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_order_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->integer('price')->default(0);
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_comparations');
    }
};
