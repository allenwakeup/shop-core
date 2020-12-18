<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_connections', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedInteger('datasource_id')->comment('数据源ID');
            $table->string('name', 50)->nullable(false)->comment('名称');
            $table->string('description', 255)->nullable ()->comment ('描述');
            $table->string('conn_type', 50)->nullable()->default ('')->comment('连接类型');
            $table->string('tns', 255)->nullable()->default ('')->comment('透明网络底层 TNS管理和配置Oracle数据库和客户端连接的工具');
            $table->string('driver', 50)->nullable(false)->comment('驱动名');
            $table->string('host', 255)->nullable()->comment('主机名或IP地址');
            $table->unsignedInteger('port')->nullable()->comment('端口号 大于1000');
            $table->string ('database', 50)->nullable (false)->comment ('数据库名');
            $table->string ('username', 50)->nullable ()->comment ('用户名');
            $table->string ('password', 50)->nullable ()->comment ('密码');
            $table->string ('url', 255)->nullable ()->comment ('URL');
            $table->string ('service_name', 100)->nullable ()->comment ('oracle service');
            $table->string ('unix_socket', 255)->nullable ()->comment ('mysql Unix socket路径');
            $table->string ('charset', 50)->nullable ()->default ('utf8mb4')->comment ('字符编码');
            $table->string ('collation', 50)->nullable ()->default ('utf8mb4_unicode_ci')->comment ('字符集');
            $table->string ('prefix', 20)->nullable ()->default ('')->comment ('表前缀名');
            $table->string ('prefix_schema', 50)->nullable ()->default ('')->comment ('schema前缀名');
            $table->tinyInteger('strict')->nullable ()->default (0)->comment ('strict 0 false 1 true');
            $table->string ('engine', 20)->nullable ()->comment ('mysql');
            $table->string ('schema', 20)->nullable ()->default ('public')->comment ('pgsql');
            $table->string ('edition', 50)->nullable ()->default ('ora$base')->comment ('oracle 版本使用限制');
            $table->string ('server_version', 50)->nullable ()->default ('11g')->comment ('oracle 版本');
            $table->string ('sslmode', 20)->nullable ()->default ('prefer')->comment ('pgsql');
            $table->text ('options')->nullable()->comment('附加设置 json');
            $table->string('type')->nullable (false)->default('SRC')->comment('分类： SRC 来源 DST 目标');
            $table->string('group', 50)->default('')->comment('分组');
            $table->unsignedInteger('order')->default(0)->comment ('排序');
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
        Schema::dropIfExists('core_connections');
    }
}
