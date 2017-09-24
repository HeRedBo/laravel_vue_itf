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
        // 道馆课程表
        Schema::create('schedules', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('class_id')->default('0')->comment('班级ID');
            $table->time('coach_id')->commnet('教练ID');
            $table->time('week')->commnet('星期几');
            $table->tinyInteger('section')->default('0')->comment('节次 表示第几节课');
            $table->time('start_time')->commnet('课程有效期开始时间');
            $table->time('end_time')->commnet('课程效期结束时间');
            $table->string('remark', 255)->comment('课程备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->dateTime('created_at')->comment('创建时间');
            $table->index('operator_id');
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
