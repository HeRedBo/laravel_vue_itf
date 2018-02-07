<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminCourseTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_course_time', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->unsigned()->comment('道馆ID');
            $table->integer('schedule_id')->unsigned()->comment('课程表ID');
            $table->tinyInteger('section')->default('0')->comment('节次 表示第几节课');
            $table->time('start_time')->commnet('课程有效期开始时间');
            $table->time('end_time')->commnet('课程效期结束时间');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->dateTime('created_at')->comment('创建时间');
            $table->index('operator_id');
            $table->comment = '后台道馆课程表额外配置表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_time');
    }
}
