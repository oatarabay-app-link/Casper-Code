<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments__checks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->timestamp('create_date')->nullable();
            $table->string('subscription_uuid')->nullable();
            $table->string('user_uuid')->nullable();
            $table->integer('user_id')->unsigned();
            $table->string('user_email')->nullable();
            $table->integer('subscription_id')->unsigned();
            $table->text('token')->nullable();
            $table->text('status')->nullable();
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
        Schema::drop('payments__checks');
    }
}
