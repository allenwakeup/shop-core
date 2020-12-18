<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_model_mappings', function (Blueprint $table) {
            $table->string('left_type', 50)->nullable(false)->comment('多态类型名称');
            $table->unsignedInteger('left_id')->nullable(false)->comment('多态ID');
            $table->string('right_type', 50)->nullable(false)->comment('多态类型名称');
            $table->unsignedInteger('right_id')->nullable(false)->comment('多态ID');

            $table->unique(['left_type', 'left_id', 'right_type', 'right_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_model_mappings');
    }
}
