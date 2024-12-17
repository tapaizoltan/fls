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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('productmaincategory_id')->comment('főkategória');
            $table->integer('productsubcategory_id')->nullable()->comment('alkategória');
            $table->integer('brand_id')->nullable()->comment('márka');
            $table->string('image_path')->nullable()->comment('kép a termékről');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
