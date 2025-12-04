<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->string('subscription_uuid')->nullable();
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();
            $table->string('vpn_pass')->nullable();
            $table->boolean('is_active')->nullable();
            $table->integer('subscription_id')->unsigned();
            $table->integer('user_id')->unsigned();
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
        Schema::drop('user_subscriptions');
    }
}
