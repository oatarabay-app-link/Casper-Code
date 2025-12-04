<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRadGroupRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rad_group_replies', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('groupname')->nullable();
            $table->string('attribute')->nullable();
            $table->string('op')->nullable();
            $table->string('value')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rad_group_replies');
    }
}
