<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSubscriptionExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscription_extensions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->integer('subscription_id')->unsigned();
            $table->integer('days')->nullable();
            $table->timestamp('exipry_date')->nullable();
            $table->longText('note')->nullable();
            $table->integer('added_by')->unsigned();
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
        Schema::drop('user_subscription_extensions');
    }
}
