<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsContinousDiscountColumnInPaymentHasClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_has_clients', function (Blueprint $table) {
            $table->boolean('is_continuous_discount')->default(0)->after('no_disount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_has_clients', function (Blueprint $table) {
            //
        });
    }
}
