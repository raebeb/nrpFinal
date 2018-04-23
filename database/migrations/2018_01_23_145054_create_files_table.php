<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('schedule_id');
            $table->string('receiver');
            $table->string('name_receiver');
            $table->string('lastname_receiver');
            $table->string('file_name');
            $table->string('local_path');
            $table->string('storage_path');
            //input = 1, output = 2
            $table->integer('file_io');
            $table->string('period');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
