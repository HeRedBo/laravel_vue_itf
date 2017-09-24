<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->string('name', 50)->default('')->comment('班级名称');
            $table->string('remark', 255)->comment('班级备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamps();
            $table->index('operator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
