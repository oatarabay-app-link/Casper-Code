<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->string('subscription_uuid')->nullable();
            $table->integer('subscription_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('period_in_months')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('status')->nullable();
            $table->float('payment_sum')->nullable();
            $table->longText('details')->nullable();
            $table->longText('check_code')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('user_id')->references('id')->on('users');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments');
    }
}
