<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionProtocolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_protocols', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('subscription_uuid')->nullable();
            $table->string('protocol_uuid')->nullable();
            $table->integer('protocol_id')->unsigned();
            $table->integer('subscription_id')->unsigned();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('protocol_id')->references('id')->on('protocols');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subscription_protocols');
    }
}
