<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuperCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('super_calls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_id')->unsigned();
            $table->string('initial_payload', 4096);
            $table->string('final_response', 4096)->nullable();
            $table->string('status');
            $table->timestamps();

            $table->foreign('operation_id')->references('id')->on('operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('super_calls');
    }
}
