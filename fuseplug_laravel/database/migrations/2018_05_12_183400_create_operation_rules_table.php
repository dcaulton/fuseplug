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
            $table->integer('acting_on');
            $table->boolean('do_always')->default(false);
            $table->integer('input_selector');
            $table->integer('operator');
            $table->integer('allowed_value');
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
        Schema::dropIfExists('operation_rules');
    }
}
