<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('n_a_s', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('nasname')->nullable();
            $table->string('shortname')->nullable();
            $table->string('type')->nullable();
            $table->integer('ports')->nullable();
            $table->string('secret')->nullable();
            $table->string('server')->nullable();
            $table->string('community')->nullable();
            $table->string('description')->nullable();
            $table->longText('details')->nullable();
            $table->longText('check_code')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('n_a_s');
    }
}
