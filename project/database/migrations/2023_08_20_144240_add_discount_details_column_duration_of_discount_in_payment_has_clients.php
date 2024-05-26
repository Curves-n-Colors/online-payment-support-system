<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountDetailsColumnDurationOfDiscountInPaymentHasClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_has_clients', function (Blueprint $table) {
            $table->boolean('discount_type')->default(1)->after('uuid');
            $table->string('discount')->nullable()->after('discount_type');
            $table->integer('no_disount')->default(0)->nullable()->after('discount');
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
