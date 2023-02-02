<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInPaymentSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_setups', function (Blueprint $table) {
            $table->date('expire_date')->after('reference_date')->nullable();
            $table->integer('no_of_payments')->nullable();
            $table->integer('extended_days')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_setups', function (Blueprint $table) {
            //
        });
    }
}
