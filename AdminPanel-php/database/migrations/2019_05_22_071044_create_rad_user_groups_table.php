<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRadUserGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rad_user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('username')->nullable();
            $table->string('groupname')->nullable();
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
        Schema::drop('rad_user_groups');
    }
}
