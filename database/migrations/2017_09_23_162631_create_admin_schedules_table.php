<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 后台课程明细表
        Schema::create('admin_schedules', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('class_id')->default('0')->comment('班级ID');
            $table->time('week')->commnet('星期几');
            $table->tinyInteger('section')->default('0')->comment('节次 表示第几节课');
            $table->string('remark', 255)->comment('课程备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->dateTime('created_at')->comment('创建时间');
            $table->index('operator_id');
            $table->index('class_id');
            $table->comment = '后台课程明细表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_schedules');
    }
}
