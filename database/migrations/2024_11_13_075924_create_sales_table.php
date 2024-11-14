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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('user_id');
            $table->string('sale_event_id')->comment('értékesítési esemény azonosító: SE-00000001');
            $table->string('sale_event_key')->comment('értékesítési esemény kulcs: pl.: eB1xB7xBjpgn');
            $table->tinyInteger('status')->unsigned()->comment('feltöltés enumból: SalesStatus'); // státusz: igényfelmérés, ajánlat adás, ajánlat utánkövetés, szerződéskötés számlázás, lezárt nyert, lezárt vesztett, kiszállítás alatt, átvéve, értékeslés, 
            $table->tinyInteger('where_did_a_find_us')->nullable()->comment('hol talált ránk?, feltöltés enumból: WhereDidAFindUs'); // igényfelmérés
            $table->longText('what_are_you_interested_in')->nullable()->comment('mi iránt érdeklődik?'); // igényfelmérés
            $table->tinyInteger('failed_transaction_status')->unsigned()->nullable()->comment('sikertelen ügylet státusz'); // lezárt vesztett
            $table->longText('description_of_failed_transaction')->nullable()->comment('elvesztés okának leírása'); // lezárt vesztett
            $table->tinyInteger('sale_evaluation')->unsigned()->nullable()->comment('Értékesítés értékelése 0-9: feltöltés enumból: SalesEvaluationStatus'); // értékesítés értékeslése
            $table->string('satisfaction_survey_id')->nullable()->comment('elégedettségi felmérés kérdéscsomag azonosító: SA-00000001 vagy sA-UxEW0jGs'); // visszamérés
            $table->string('backtracking_id')->nullable()->comment('értékesítési felmérés válasz azonosító BA-00000001 vagy bA-UxEW0jGs'); // visszamérés
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
