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
            $table->string('name')->nullable();
            $table->longText('description')->nullable()->comment('leírás');
            $table->string('image_path')->nullable()->comment('kép a termékről');
            // szín, méret
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
