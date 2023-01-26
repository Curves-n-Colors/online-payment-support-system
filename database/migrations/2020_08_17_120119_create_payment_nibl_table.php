<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentNiblTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_nibl', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('bank_ref_id');
            $table->string('ipg_txn_id', 32);
            $table->string('mer_ref_id', 32);
            $table->boolean('status');
            $table->datetime('server_time');
            $table->string('masked_acc_number', 32)->nullable();
            $table->string('card_holder_name', 32)->nullable();
            $table->text('fail_reason')->nullable();
            $table->text('auth_code')->nullable();
            $table->float('refund_amount', 15, 2)->unsigned()->nullable();
            $table->boolean('refund_status')->nullable();
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
        Schema::dropIfExists('payment_nibl');
    }
}
