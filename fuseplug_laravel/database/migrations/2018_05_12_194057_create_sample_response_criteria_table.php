<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleResponseCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_response_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sample_request_id')->unsigned();
            $table->integer('order');
            $table->string('response_selector');
            $table->string('expected_value');
            $table->timestamps();

            $table->foreign('sample_request_id')->references('id')->on('sample_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_response_criteria');
    }
}
