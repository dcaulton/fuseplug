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
            $table->string('source_field');
            $table->string('source_field_type')->nullable();
            $table->string('target_field');
            $table->string('target_data_type')->nullable();
            $table->string('target_format_string')->nullable();
            $table->string('transform')->nullable();
            $table->boolean('skip_if_empty')->default(false);
            $table->string('default_value')->nullable();

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
