<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_setups', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('email');
            $table->uuid('uuid')->unique();
            $table->boolean('is_active');
            $table->boolean('is_advance');
            $table->text('remarks')->nullable();
            $table->json('contents')->nullable();
            $table->float('total', 15, 2)->unsigned();
            $table->string('currency', 8);
            $table->json('payment_options')->nullable();
            $table->integer('recurring_type')->nullable();
            $table->date('reference_date')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('recurring_payments');
    }
}
