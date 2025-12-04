<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVPNServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v_p_n_servers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->timestamp('create_date')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->boolean('is_disabled')->nullable();
            $table->string('ip')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('name')->nullable();
            $table->string('country')->nullable();
            $table->longText('parameters')->nullable();
            $table->string('server_provider')->nullable();
            $table->longText('notes')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('v_p_n_servers');
    }
}
