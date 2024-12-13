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
        Schema::table('items', function (Blueprint $table) {
            $table->string('no_invoice')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_addrees')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('no_invoice');
            $table->dropColumn('supplier_name');
            $table->dropColumn('supplier_addrees');
        });
    }
};
