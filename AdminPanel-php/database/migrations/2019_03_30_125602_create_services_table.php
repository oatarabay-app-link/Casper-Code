<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->double('amount')->nullable();
            $table->boolean('is_recurring')->nullable();
            $table->boolean('is_autobill')->nullable();
            $table->double('setup_fee')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->boolean('notify')->nullable();
            $table->integer('notify_days')->nullable();
            $table->integer('service_provider_id')->unsigned();
            $table->longText('notes')->nullable();
            $table->foreign('service_provider_id')->references('id')->on('service_providers');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('services');
    }
}
