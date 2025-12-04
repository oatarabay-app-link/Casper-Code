<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFolderAndFilenameToIntercomData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('intercom_marketing_datas', function (Blueprint $table) {
            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intercom_marketing_datas', function (Blueprint $table) {
            //
        });
    }
}
