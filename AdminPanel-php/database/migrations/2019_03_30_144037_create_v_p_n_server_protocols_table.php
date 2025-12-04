<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVPNServerProtocolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v_p_n_server_protocols', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('vpnserver_uuid')->nullable();
            $table->string('protocol_uuid')->nullable();
            $table->integer('vpnserver_id')->unsigned();
            $table->integer('protocol_id')->unsigned();
            $table->foreign('vpnserver_id')->references('id')->on('v_p_n_servers');
            $table->foreign('protocol_id')->references('id')->on('protocols');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('v_p_n_server_protocols');
    }
}
