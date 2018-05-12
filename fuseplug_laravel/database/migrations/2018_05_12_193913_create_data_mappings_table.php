<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_action_id')->unsigned();
            $table->string('brand_versions');
            $table->string('fuse_versions');
            $table->string('template');
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
        Schema::dropIfExists('data_mappings');
    }
}
