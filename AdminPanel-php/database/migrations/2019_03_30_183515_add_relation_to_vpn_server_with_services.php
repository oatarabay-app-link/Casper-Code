<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationToVpnServerWithServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v_p_n_servers', function (Blueprint $table) {


            $table->integer('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v_p_n_servers', function (Blueprint $table) {
            //
        });
    }
}
