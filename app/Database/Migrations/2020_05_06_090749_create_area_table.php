<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 20)->nullable(false)->comment('国家标准行政地区编码 如430302');
            $table->string('name', 50)->nullable(false)->comment('国家标准行政地区编码名称');
            $table->string('short', 50)->nullable()->comment('简称');
            $table->string('alias', 50)->nullable()->comment('别名');
            $table->string('display', 50)->nullable()->comment('地区显示名称');
            $table->string ('description', 255)->nullable ()->comment ('描述');
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
        Schema::dropIfExists('core_areas');
    }
}
