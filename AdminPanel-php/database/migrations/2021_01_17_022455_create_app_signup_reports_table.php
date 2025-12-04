<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSignupReportsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_signup_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('email');
            $table->string('status');
            $table->dateTime('signup_date');
            $table->dateTime('signedin_date');
            $table->string('subscription');
            $table->integer('emails_sent');
            $table->integer('emails_problems');
            $table->string('device');
            $table->string('Country');
            $table->string('OS');
            $table->timestamp('last_seen');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_signup_reports');
    }
}
