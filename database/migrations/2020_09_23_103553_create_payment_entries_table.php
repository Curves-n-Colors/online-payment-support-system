<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_setup_id')->unsigned();
            $table->string('title', 100)->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('email');
            $table->uuid('uuid')->unique();
            $table->boolean('is_active');
            $table->json('contents')->nullable();
            $table->float('total', 15,2)->unsigned();
            $table->string('currency', 8);
            $table->json('payment_options')->nullable();
            $table->date('payment_date')->nullable();
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
        Schema::dropIfExists('payment_entries');
    }
}
