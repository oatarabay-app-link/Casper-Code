<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('provider_type')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_providers');
    }
}
