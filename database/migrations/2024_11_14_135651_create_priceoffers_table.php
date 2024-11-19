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
        Schema::create('priceoffers', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('user_id');
            $table->string('price_offer_id')->comment('árajánlat azonosító: PO-00000001');
            $table->tinyInteger('status')->unsigned()->comment('feltöltés enumból: PriceOffersStatus');
            $table->date('expected_closing_at')->comment('várható lezárás dátuma');
            $table->integer('offer_amount')->nullable()->comment('árajánlat összege, várható árbevétel');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priceoffers');
    }
};
