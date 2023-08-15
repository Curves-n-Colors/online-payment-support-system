<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHblTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_hbl', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('amount', 32);
            $table->string('eci', 32);
            $table->string('currency_code', 12);
            $table->string('invoice_no', 32)->nullable();
            $table->string('tran_ref', 32)->nullable();
            $table->string('response_code', 32)->nullable();
            $table->string('approval_code', 32)->nullable();
            $table->string('fraud_code', 32)->nullable();
            $table->boolean('status')->nullable();
            $table->date('transaction_at')->nullable();
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
        Schema::dropIfExists('payment_hbl');
    }
}
