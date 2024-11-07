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
            $table->integer('customer_id'); // kötelező kitölteni
            $table->integer('user_id'); // kötelező kitölteni
            $table->tinyInteger('event_type')->unsigned()->comment('feltöltés enumból: EventTypes'); // kötelező kitölteni
            $table->tinyInteger('status')->unsigned()->comment('feltöltés enumból: SalesStatus'); // kötelező kitölteni
            $table->string('where_did_a_find_us')->comment('hol talált ránk?'); // kötelező kitölteni
            $table->longText('what_are_you_interested_in')->nullable()->comment('mi iránt érdeklődik?'); // kötelező kitölteni
            $table->date('date_of_offer')->comment('ajánlatadás dátuma'); // kötelező kitölteni
            $table->integer('expected_sales_revenue')->nullable()->comment('várható árbevétel');
            $table->date('expected_closing_date')->comment('várható lezárás dátuma'); // kötelező kitölteni
            $table->longText('sales_info')->nullable()->comment('értékesítési infók');
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
