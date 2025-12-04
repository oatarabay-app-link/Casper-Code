<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCaspervpnFeildsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_login')->nullable();
            $table->string('old_user_login');
            $table->string('phone');
            $table->uuid('user_subscription_id');
            $table->boolean('is_blocked')->default(config('access.users.confirm_email') ? false : true);
            $table->boolean('is_deleted')->default(config('access.users.confirm_email') ? false : true);
            $table->string('description')->nullable();
            $table->string('tsv')->nullable();
            $table->string('affliate_ref')->nullable();
            $table->timestamp('last_active_date')->nullable();
            $table->timestamp('create_date')->nullable();

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
            $table->string('user_login')->nullable();
            $table->string('old_user_login');
            $table->string('phone');
            $table->uuid('user_subscription_id');
            $table->boolean('is_blocked')->default(config('access.users.confirm_email') ? false : true);
            $table->boolean('is_deleted')->default(config('access.users.confirm_email') ? false : true);
            $table->string('description')->nullable();
            $table->string('tsv')->nullable();
            $table->string('affliate_ref')->nullable();
            $table->timestamp('last_active_date')->nullable();
            $table->timestamp('create_date')->nullable();
        });
    }
}
