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
        Schema::create('saleevents', function (Blueprint $table) {
            $table->id(); 
            $table->integer('customer_id');
            $table->integer('user_id');
            $table->tinyInteger('event_type')->unsigned()->nullable()->comment('feltöltés enumból: EventTypes');
            $table->tinyInteger('status')->unsigned()->nullable()->comment('feltöltés enumból: SalesStatus');
            $table->tinyInteger('where_did_a_find_us')->nullable()->comment('hol talált ránk?');
            $table->longText('what_are_you_interested_in')->nullable()->comment('mi iránt érdeklődik?');
            $table->date('date_of_offer')->nullable()->comment('ajánlatadás dátuma');
            $table->integer('expected_sales_revenue')->nullable()->comment('várható árbevétel');
            $table->date('expected_closing_date')->nullable()->comment('várható lezárás dátuma');
            $table->longText('sales_info')->nullable()->comment('értékesítési infók');
            $table->tinyInteger('failed_transaction_status')->unsigned()->nullable()->comment('sikertelen ügylet státusz: feltöltés enumból: FailedStatus');
            $table->longText('description_of_failed_transaction')->nullable()->comment('elvesztés okának leírása');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saleevents');
    }
};
