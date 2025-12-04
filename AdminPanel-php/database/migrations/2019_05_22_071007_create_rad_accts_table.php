<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRadAcctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rad_accts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('groupname')->nullable();
            $table->string('realm')->nullable();
            $table->string('nasipaddress')->nullable();
            $table->string('nasidentifier')->nullable();
            $table->string('nasportid')->nullable();
            $table->string('nasporttype')->nullable();
            $table->dateTime('acctstarttime')->nullable();
            $table->dateTime('acctstoptime')->nullable();
            $table->integer('acctsesslontime')->nullable();
            $table->string('acctauthentic')->nullable();
            $table->string('connectinfo_start')->nullable();
            $table->string('connectinfo_stop')->nullable();
            $table->integer('acctinputoctest')->nullable();
            $table->string('acctoutputoctest')->nullable();
            $table->string('calledstationid')->nullable();
            $table->string('callingstationid')->nullable();
            $table->string('acctterminatecause')->nullable();
            $table->string('servicetype')->nullable();
            $table->string('framedprotocol')->nullable();
            $table->string('framedipaddress')->nullable();
            $table->integer('acctstartdelay')->nullable();
            $table->integer('acctstopdelay')->nullable();
            $table->string('xascendsessionsvrkey')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rad_accts');
    }
}
