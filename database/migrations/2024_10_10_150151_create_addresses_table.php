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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('addresstype_id')->unsigned();
            $table->string('country_code', length: 3);
            $table->string('zip_code', length: 10);
            $table->string('settlement');
            $table->string('address')->nullable();
            $table->integer('area_type_id')->nullable()->comment('feltöltése area_types táblából');
            $table->string('address_number')->nullable();
            $table->string('description')->nullable()->comment('emelet, ajtó');
            $table->tinyInteger('parcel_type')->unsigned()->nullable()->comment('külterület vagy belterület');
            $table->string('parcel_number')->nullable()->comment('helyrajzi szám');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('po_box')->nullable()->comment('postafiók');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
