<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraColumnsForStatusOfSubscription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_has_clients', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->date('reference_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->boolean('is_active');
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
