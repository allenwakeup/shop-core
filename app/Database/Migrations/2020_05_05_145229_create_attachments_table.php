<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachable_type', 50)->nullable(false)->comment('多态类型名称');
            $table->unsignedInteger('attachable_id')->nullable(false)->comment('多态ID');
            $table->string('name', 50)->nullable(false)->comment('原始文件名');
            $table->unsignedInteger('size')->nullable(false)->comment('文件大小');
            $table->string('ext', 10)->nullable(false)->comment('文件后缀');
            $table->string('ext_name', 20)->nullable(false)->comment('后缀类型');
            $table->string('path', 255)->comment('文件存储路径');
            $table->tinyInteger('status')->nullable(false)->default(1)->comment('状态');
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
        Schema::dropIfExists('core_attachments');
    }
}
