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
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('approved_by')->index()->nullable();
            $table->unsignedBigInteger('project_id')->index();
            $table->integer('amount')->default(0);
            $table->integer('status')->comment('1 = Pending, 2 = Approved, 3 = Declined');
            $table->timestamps();
        });

        Schema::table('management_projects', function (Blueprint $table) {
            $table->integer('petty_cash')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cashes');
    }
};
