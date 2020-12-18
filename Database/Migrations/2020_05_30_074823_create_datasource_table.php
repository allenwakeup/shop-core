<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatasourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_datasources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 50)->nullable(false)->comment('代码');
            $table->string('name', 50)->nullable(false)->comment('名称');
            $table->string ('description', 255)->nullable ()->comment ('描述');
            $table->text('requires')->nullable(false)->comment('必填字段');
            $table->text('options')->nullable()->comment('选填字段');
            $table->unsignedInteger('order')->default(0)->comment('排序');
            $table->tinyInteger('status')->nullable(false)->default(1)->comment('状态 0 未启用 1 启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_datasources');
    }
}
