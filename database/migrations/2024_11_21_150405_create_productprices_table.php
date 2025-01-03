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
        Schema::create('productprices', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('net_purchase_price_eur')->nullable()->comment('netto beszerzési ár (euro)');
            $table->integer('net_purchase_price_huf')->nullable()->comment('netto beszerzési ár (forint)');
            $table->integer('net_list_price_eur')->nullable()->comment('netto lista ár (euro)');
            $table->integer('net_list_price_huf')->nullable()->comment('netto lista ár (forint)');
            $table->integer('profit_margin')->nullable()->comment('haszonkulcs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productprices');
    }
};
