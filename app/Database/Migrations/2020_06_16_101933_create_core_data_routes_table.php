<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreDataRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_data_routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->nullable(false)->comment('路径名称');
            $table->string('alias', 50)->nullable()->comment('路径别名');
            $table->string('short', 20)->nullable()->comment('路径简称');
            $table->string('description', 255)->nullable()->comment('路径描述');
            $table->string('from', 100)->nullable(false)->comment('头表名称');
            $table->string('table_from', 100)->nullable(false)->comment('头表');
            $table->string('to', 100)->nullable(false)->comment('尾表名称');
            $table->string('table_to', 100)->nullable(false)->comment('尾表');
            $table->string('output', 100)->nullable()->comment('输出表');
            $table->unsignedInteger('connection_id')->nullable()->default (0)->comment('使用数据连接');
            $table->timestamps();

            $table->unique (['table_from', 'table_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_data_routes');
    }
}
