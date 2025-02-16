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
        Schema::create('upload_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_order_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name')->nullable();
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
        Schema::dropIfExists('upload_invoices');
    }
};
