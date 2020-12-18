<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreDataMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_data_mappings', function (Blueprint $table) {
            $table->string('left_type', 100)->nullable(false)->comment('多态类型名称');
            $table->string('left_id', 50)->nullable(false)->comment('多态ID');
            $table->string('right_type', 100)->nullable(false)->comment('多态类型名称');
            $table->string('right_id', 50)->nullable(false)->comment('多态ID');
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
        Schema::dropIfExists('core_data_mappings');
    }
}
