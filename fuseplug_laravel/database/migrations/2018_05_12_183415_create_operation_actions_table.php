<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_rule_id')->unsigned();
            $table->integer('order');
            $table->string('name')->unique();
            $table->string('queue')->nullable();
            $table->string('operation_type');
            $table->string('operation_source')->nullable;
            $table->string('extra_parameters', 1024)->nullable();
            $table->string('http_verb')->nullable;

            $table->foreign('operation_rule_id')->references('id')->on('operation_rules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_actions');
    }
}
