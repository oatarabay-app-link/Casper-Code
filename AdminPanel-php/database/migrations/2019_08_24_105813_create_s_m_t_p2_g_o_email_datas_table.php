<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSMTP2GOEmailDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_m_t_p2_g_o_email_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('subject')->nullable();
            $table->string('delivered_at')->nullable();
            $table->string('process_status')->nullable();
            $table->string('email_id')->nullable();
            $table->string('status')->nullable();
            $table->string('response')->nullable();
            $table->string('email_tx')->nullable();
            $table->string('host')->nullable();
            $table->string('smtpcode')->nullable();
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->string('stmp2gousername')->nullable();
            $table->longText('headers')->nullable();
            $table->string('total_opens')->nullable();
            $table->longText('opens')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('s_m_t_p2_g_o_email_datas');
    }
}
