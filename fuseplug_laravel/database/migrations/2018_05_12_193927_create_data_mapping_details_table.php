<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataMappingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_mapping_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data_mapping_id')->unsigned();
            $table->integer('order');
            $table->integer('function_name');
            $table->integer('source_field');
            $table->integer('target_field');
            $table->integer('target_data_type');
            $table->integer('target_format_string');
            $table->integer('transform');

            $table->foreign('data_mapping_id')->references('id')->on('data_mappings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_mapping_details');
    }
}
