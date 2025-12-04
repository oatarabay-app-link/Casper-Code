<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LoginPasswordPrincipal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login')->nullable();
            $table->string('old_login')->nullable();
            $table->uuid('login_pass_id')->nullable();
            $table->boolean('is_confirmed')->nullable();
            $table->string('confirm_code')->nullable();
            $table->integer('version')->nullable();
            $table->timestamp('create_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
