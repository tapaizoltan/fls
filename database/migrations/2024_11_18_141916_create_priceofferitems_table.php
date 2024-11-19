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
        Schema::create('priceofferitems', function (Blueprint $table) {
            $table->id();
            $table->integer('priceoffer_id')->comment('árajánlat azonosító');
            $table->integer('product_id')->comment('termék');
            $table->integer('netprice')->comment('nettó ár');
            $table->integer('quantity')->comment('mennyiség');
            $table->integer('discount')->comment('kedvezmény mértéke');
            $table->integer('net_total_price')->comment('nettó össz. ár (kedvezménnyel együtt)');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priceofferitems');
    }
};
