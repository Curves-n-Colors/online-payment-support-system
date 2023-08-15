<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentConnectIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_connect_ips', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('merchant_id')->nullable();
            $table->string('app_id')->nullable();
            $table->string('txn_amt')->nullable();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_connect_ips');
    }
}
