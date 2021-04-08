<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 200)->nullable(false)->comment('名称');
            $table->string('input', 500)->nullable(false)->comment('任务执行命令或者任务类');
            $table->string('cron', 50)->nullable(false)->comment('任务周期设置 * * * * *');
            $table->string('ping_before', 500)->nullable()->comment('任务执行前ping指定的URL');
            $table->string('ping_success', 500)->nullable()->comment('任务执行成功后ping指定的URL');
            $table->string('ping_failure', 500)->nullable()->comment('任务执行失败后ping指定的URL');
            $table->text('payload')->nullable()->comment('附加设置 json');
            $table->string('description', 255)->nullable ()->comment ('描述');
            $table->tinyInteger('schedule_type')->nullable(false)->default(1)->comment('任务类型 1 Command 2 Exec 3 Job');
            $table->tinyInteger('overlapping')->nullable(false)->default(0)->comment('之前的任务实例还在执行，调度内的任务 0 不会 1 会 执行');
            $table->tinyInteger('one_server')->nullable(false)->default(0)->comment('任务只运行在 0 多台 1 一台 服务器上');
            $table->tinyInteger('background')->nullable(false)->default(0)->comment('0 前台任务 1 后台任务');
            $table->tinyInteger('maintenance')->nullable(false)->default(0)->comment('系统维护模式下 0 不执行 1 仍然执行');
            $table->tinyInteger('once')->nullable(false)->default(0)->comment('status 启用时 仅执行一次 0 未启用 1 启用');
            $table->string('group', 20)->nullable()->default('')->comment('分组名称');
            $table->unsignedInteger('order')->default(0)->comment ('排序');
            $table->tinyInteger('status')->nullable(false)->default(0)->comment('状态 0 未启用 1 启用');
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
        Schema::dropIfExists('core_schedules');
    }
}
