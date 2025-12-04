<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->string('subscription_name')->nullable();
            $table->decimal('monthly_price')->nullable();
            $table->decimal('period_price')->nullable();
            $table->string('currency_type')->nullable();
            $table->bigInteger('traffic_size')->nullable();
            $table->bigInteger('rate_limit')->nullable();
            $table->integer('max_connections')->nullable();
            $table->boolean('available_for_android')->nullable();
            $table->boolean('available_for_ios')->nullable();
            $table->timestamp('create_time')->nullable();
            $table->boolean('is_default')->nullable();
            $table->integer('period_length')->nullable();
            $table->integer('order_num')->nullable();
            $table->integer('product_id')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subscriptions');
    }
}
