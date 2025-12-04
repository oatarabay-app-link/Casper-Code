<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRadiusDefaultAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radius_default_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('attribute')->nullable();
            $table->string('op')->nullable();
            $table->string('value')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('status')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('radius_default_attributes');
    }
}
