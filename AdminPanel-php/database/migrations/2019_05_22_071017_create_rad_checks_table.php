<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRadChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rad_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('username')->nullable();
            $table->string('attribute')->nullable();
            $table->string('op')->nullable();
            $table->string('value')->nullable();
            $table->string('protocol')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rad_checks');
    }
}
