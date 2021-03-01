<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('county', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64)->nullable(false)->comment('国家标准行政地区编码名称');
            $table->string('county_id', 12)->nullable(false)->comment('国家标准行政地区编码ID');
            $table->string('city_id', 12)->nullable(false)->comment('国家标准行政地区编码ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('county');
    }
}
