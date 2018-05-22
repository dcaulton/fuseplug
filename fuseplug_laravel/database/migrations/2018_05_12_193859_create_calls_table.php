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
            $table->string('request_data', 1024);
            $table->string('response_data', 4096)->nullable();
            $table->string('error_messages', 4096)->nullable();
            $table->string('debug_info', 4096)->nullable();
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
