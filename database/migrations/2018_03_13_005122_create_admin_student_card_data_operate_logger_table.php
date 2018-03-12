<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminStudentCardDataOperateLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_student_card_data_operate_logger', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->string('type',60)->default('')->comment('数据类型,自定义的数据类型格式 如 order,card');
            $table->unsignedInteger('student_id')->default('0')->comment('学生记录ID');
            $table->unsignedInteger('student_card_id')->default('0')->comment('学生卡券记录ID');
            $table->string('operation',255)->default('')->comment('操作描述');
            $table->text('content')->comment('日志内容 存json数据格式');
            $table->unsignedInteger('operator_id')->default('0')->comment('操作用户ID');
            $table->string('operator_name',60)->default('')->comment('操作用户姓名');
            $table->timestamp('created_at')->nullable()->comment('操作时间');
            $table->index(['type','student_id']);
            $table->index(['student_card_id']);
            $table->index('operator_id');
            $table->comment = '后台卡券数据操作日志记录表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_student_card_data_operate_logger');
    }
}
