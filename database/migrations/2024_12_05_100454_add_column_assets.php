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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('brand')->after('category')->nullable();
            $table->string('owner')->after('color')->nullable();
            $table->string('type_purchase')->after('purchase_date')->nullable();
            $table->string('contract_period')->after('type_purchase')->nullable();
            $table->string('file_reminder')->after('contract_period')->nullable();
            $table->date('date_reminder')->after('file_reminder')->nullable();
            $table->string('no_policy')->after('date_reminder')->nullable();
            $table->string('insurance_name')->after('no_policy')->nullable();
            $table->integer('insurance_cost')->after('insurance_name')->nullable();
            $table->integer('tax_cost')->after('insurance_cost')->nullable();
            $table->string('tax_period')->after('tax_cost')->nullable();
            $table->string('file_tax')->after('tax_period')->nullable();
            $table->date('date_tax')->after('file_tax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'owner',
                'type_purchase',
                'contract_period',
                'file_reminder',
                'date_reminder',
                'no_policy',
                'insurance_name',
                'insurance_cost',
                'tax_cost',
                'tax_period',
                'file_tax',
                'date_tax'
            ]);
        });
    }
};
