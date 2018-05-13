<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_id')->unsigned();
            $table->string('brand_version');
            $table->string('fuse_version');
            $table->integer('order');
            $table->string('acting_on');
            $table->boolean('do_always')->default(false);
            $table->string('input_selector');
            $table->string('operator');
            $table->string('allowed_value');

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
        Schema::dropIfExists('operation_rules');
    }
}
