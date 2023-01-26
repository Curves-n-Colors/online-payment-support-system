<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_setup_id')->unsigned();
            $table->string('title', 100)->unique();
            $table->integer('client_id')->unsigned();
            $table->string('email');
            $table->string('ref_code', 32)->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->json('contents')->nullable();
            $table->float('total', 15,2)->unsigned();
            $table->string('currency', 8);
            $table->date('payment_date')->nullable();
            $table->string('payment_type', 16)->nullable();
            $table->tinyInteger('payment_status')->unsigned()->nullable();
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
        Schema::dropIfExists('payment_details');
    }
}
