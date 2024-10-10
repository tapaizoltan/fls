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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('cég vagy szervezet neve');
            $table->string('registration_number', length:20)->comment('nyilvántartási szám');
            $table->string('tax_number', length:20)->comment('adószám');
            $table->tinyInteger('payment_deadline')->comment('fizetési határidő');
            $table->integer('unique_payment_deadline')->nullable()->comment('egyedi fizetési határidő');
            $table->longText('description')->nullable()->comment('leírás');
            $table->tinyInteger('financial_risk_rate')->nullable()->comment('pénzügyi kockázati ráta 0-9');
            $table->longText('justification_of_risk')->nullable()->comment('kockázat indokoltsága');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
