<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminVenueScheduleCourseTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_venue_schedule_course_times', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('schedule_id')->unsigned()->default('0')->comment('道馆课程表ID');
            $table->tinyInteger('section')->default('0')->comment('节次 表示第几节课');
            $table->time('start_time')->comment('课程有效期开始时间');
            $table->time('end_time')->comment('课程效期结束时间');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->dateTime('created_at')->comment('创建时间');
            $table->index(["schedule_id"]);
            $table->comment = '后台课程时间详情表';

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        chema::dropIfExists('admin_venue_schedule_course_times');
    }
}
