<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronJobNotifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_job_notify_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('start')->unsigned();
            $table->integer('limit')->unsigned();
            $table->string('recurring_type', 32);
            $table->string('advance_type', 32);
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
        Schema::dropIfExists('cron_job_notify_logs');
    }
}
