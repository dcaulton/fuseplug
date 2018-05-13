<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_action_id')->unsigned();
            $table->integer('super_call_id')->unsigned();
            $table->integer('request_data')->unsigned();
            $table->string('response_data');
            $table->string('error_messages');
            $table->string('status_code');
            $table->timestamps();

            $table->foreign('operation_action_id')->references('id')->on('operation_actions');
            $table->foreign('super_call_id')->references('id')->on('super_calls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calls');
    }
}
