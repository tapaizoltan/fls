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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('firstname')->nullable()->comment('keresztnév');
            $table->string('second_firstname')->nullable()->comment('második keresztnév');
            $table->string('lastname')->nullable()->comment('vezetéknév');
            $table->string('title')->nullable()->comment('titulusa');
            $table->string('department_name')->nullable()->comment('részleg, osztály, központ');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
