<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRadPostAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rad_post_auths', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('username')->nullable();
            $table->string('pass')->nullable();
            $table->string('reply')->nullable();
            $table->integer('priority')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rad_post_auths');
    }
}
