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
        Schema::create('productproperties', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->tinyInteger('season')->comment('évszak');
            $table->string('width', length:5)->comment('szélesség');
            $table->string('height', length:5)->comment('magasság');
            $table->string('structure' , length:1)->comment('szerkezet: R-radiál,D-diagonál,B-bias');
            $table->string('rim_diameter' , length:5)->comment('felni átmérő col');
            // Méret:Szélesség/Magasság+Gumi szerkezet+Felni átmérő
            $table->string('outer_diameter' , length:5)->comment('külső átmérő cm');
            $table->tinyInteger('load_capacity')->comment('névleges teherbírás kg');
            $table->tinyInteger('internal_structure')->comment('0->tömör, 1->fújt, 2->tömlővel és védőszalaggal, 3->töltött');
            $table->boolean('color')->comment('0 normál, 1 nyomot nem hagyó');
            $table->string('pattern_code', length:10)->comment('mintázat kód');
            $table->string('pattern_depth', length:5)->comment('mintázat mélység');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productproperties');
    }
};
