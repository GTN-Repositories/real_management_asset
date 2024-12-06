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
        Schema::table('loadsheets', function (Blueprint $table) {
            $table->string('bpit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadsheets', function (Blueprint $table) {
            $table->integer('bpit')->change(); // Assuming previous type was integer
        });
    }
};
