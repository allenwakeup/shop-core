<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreDataMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_data_maps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger ('data_route_id')->nullable(false)->default(0)->comment('数据路径');
            $table->string('left', 50)->nullable(false)->comment('左映射名称');
            $table->string('left_table', 100)->nullable(false)->comment('左映射表名');
            $table->string('left_tpl', 500)->nullable()->comment('左表数据显示模版');
            $table->string('right', 50)->nullable(false)->comment('右映射名称');
            $table->string('right_table', 100)->nullable(false)->comment('右映射表名');
            $table->string('right_tpl', 500)->nullable()->comment('右表数据显示模版');
            $table->string('relationship', 20)->nullable(false)->default('morphToMany')->comment('关联关系 可选值 多对多（多态）:morphToMany,一对多（多态）:morphTo,一对一 (多态):morphOne,一对多（多态）:morphMany,远程一对一:hasOneThrough,一对一:hasOne,远程一对多:hasManyThrough,一对多:hasMany,多对多:belongsToMany,一对多 (反向):belongsTo');
            $table->string('description', 500)->nullable()->comment('描述');
            $table->string('name', 100)->nullable()->comment('多态名 列名前缀_type');
            $table->string('table', 100)->nullable()->comment('指定表名');
            $table->string('through', 200)->nullable()->comment('远程一对多或一对一 中间模型类');
            $table->string('first_key', 100)->nullable()->comment('关联键');
            $table->string('second_key', 100)->nullable()->comment('关联键');
            $table->string('foreign_key', 100)->nullable()->comment('关联键');
            $table->string('owner_key', 100)->nullable()->comment('关联键');
            $table->string('local_key', 100)->nullable()->comment('关联键');
            $table->string('second_local_key', 100)->nullable()->comment('关联键');
            $table->string('foreign_pivot_key', 100)->nullable()->comment('关联键');
            $table->string('related_pivot_key', 100)->nullable()->comment('关联键');
            $table->string('parent_key', 100)->nullable()->comment('关联键');
            $table->string('related_key', 100)->nullable()->comment('关联键');
            $table->string('relation', 100)->nullable()->comment('关联模型名 通常是定义的属性名');
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
        Schema::dropIfExists('core_data_maps');
    }
}
