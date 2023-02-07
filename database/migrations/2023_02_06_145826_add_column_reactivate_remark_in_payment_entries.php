<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReactivateRemarkInPaymentEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_entries', function (Blueprint $table) {
            $table->boolean('is_payment_deactivate')->default(0);
            $table->string('deactivate_remark')->nullable();
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
