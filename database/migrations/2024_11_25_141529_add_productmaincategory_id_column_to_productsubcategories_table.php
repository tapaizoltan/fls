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
        Schema::table('productsubcategories', function (Blueprint $table) {
            $table->integer('productmaincategory_id')->comment('szülő kategória')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productsubcategories', function (Blueprint $table) {
            $table->dropColumn(['productmaincategory_id']);
        });
    }
};
