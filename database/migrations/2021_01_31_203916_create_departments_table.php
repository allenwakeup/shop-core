<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->default('');
            $table->unsignedInteger('pid')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['pid', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_departments');
    }
}
