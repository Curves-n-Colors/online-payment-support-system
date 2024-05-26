<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisountColumnsInpaymentEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_entries', function (Blueprint $table) {
            $table->float('discount_rate', 15, 2)->unsigned()->after('sub_total');
            $table->float('discount_amount', 15, 2)->unsigned()->after('discount_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_entries', function (Blueprint $table) {
            //
        });
    }
}
