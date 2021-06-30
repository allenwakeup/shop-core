<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_schedule_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('schedule_id')->nullable(false)->comment('计划任务ID');
            $table->string('content', 500)->nullable(false)->comment('日志内容');
            $table->tinyInteger('status')->nullable(false)->default(1)->comment('任务执行状态 1 成功');
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
        Schema::dropIfExists('core_schedule_logs');
    }
}
