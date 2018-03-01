<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdminVenueScheduleDetailExtend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_venue_schedule_detail_extend', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('schedule_id')->unsigned()->default('0')->comment('道馆课程表ID');
            $table->date('schedule_date')->comment('时间日期');
            $table->integer('class_id')->unsigned()->default('0')->comment('班级ID');
            $table->tinyInteger('week')->default('0')->comment('星期几');
            $table->tinyInteger('section')->default('0')->comment('节次 表示第几节课');
            $table->string('remark', 255)->comment('课程备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->dateTime('created_at')->comment('创建时间');
            $table->index(["schedule_id","class_id"]);
            $table->comment = '后台课程明细扩展表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_venue_schedule_detail_extend');
    }
}
