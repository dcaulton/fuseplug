<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_action_id')->unsigned();
            $table->string('request_body');
            $table->timestamps();

            $table->foreign('operation_action_id')->references('id')->on('operation_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_requests');
    }
}
