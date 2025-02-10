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
        Schema::create('request_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('total_item');
            $table->integer('total_price');
            $table->date('date');
            $table->unsignedBigInteger('warehouse_id')->index()->comment('relation warehouse');
            $table->integer('status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected');
            $table->unsignedBigInteger('created_by')->index()->comment('relation user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reques_orders');
    }
};
