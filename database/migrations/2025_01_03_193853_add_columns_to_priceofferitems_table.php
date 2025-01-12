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
        Schema::table('priceofferitems', function (Blueprint $table) {
            $table->integer('productmaincategory_id')->nullable()->comment('főkategória')->after('priceoffer_id');
            $table->integer('productsubcategory_id')->nullable()->comment('alkategória')->after('productmaincategory_id');
            $table->integer('brand_id')->nullable()->comment('márka')->after('productsubcategory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('priceofferitems', function (Blueprint $table) {
            $table->dropColumn(['productmaincategory_id']);
            $table->dropColumn(['productsubcategory_id']);
            $table->dropColumn(['brand_id']);
        });
    }
};
