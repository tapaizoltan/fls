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
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('season')->nullable()->comment('évszak')->after('image_path');
            $table->string('width', length:5)->nullable()->comment('szélesség')->after('season');
            $table->string('height', length:5)->nullable()->comment('magasság')->after('width');
            $table->string('structure' , length:1)->nullable()->comment('szerkezet: R-radiál,D-diagonál,B-bias')->after('height');
            $table->string('rim_diameter' , length:5)->nullable()->comment('felni átmérő col')->after('structure');
            // Méret:Szélesség/Magasság+Gumi szerkezet+Felni átmérő
            $table->string('outer_diameter' , length:5)->nullable()->comment('külső átmérő cm')->after('rim_diameter');
            $table->tinyInteger('load_capacity')->nullable()->comment('névleges teherbírás kg')->after('outer_diameter');
            $table->tinyInteger('internal_structure')->nullable()->comment('0->tömör, 1->fújt, 2->tömlővel és védőszalaggal, 3->töltött')->after('load_capacity');
            $table->boolean('color')->nullable()->comment('0 normál, 1 nyomot nem hagyó')->after('internal_structure');
            $table->string('pattern_code', length:10)->nullable()->comment('mintázat kód')->after('color');
            $table->string('pattern_depth', length:5)->nullable()->comment('mintázat mélység')->after('pattern_code');
            $table->longText('description')->nullable()->comment('leírás')->after('pattern_depth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['season']);
            $table->dropColumn(['width']);
            $table->dropColumn(['height']);
            $table->dropColumn(['structure']);
            $table->dropColumn(['rim_diameter']);
            $table->dropColumn(['outer_diameter']);
            $table->dropColumn(['load_capacity']);
            $table->dropColumn(['internal_structure']);
            $table->dropColumn(['color']);
            $table->dropColumn(['pattern_code']);
            $table->dropColumn(['pattern_depth']);
            $table->dropColumn(['description']);
        });
    }
};
