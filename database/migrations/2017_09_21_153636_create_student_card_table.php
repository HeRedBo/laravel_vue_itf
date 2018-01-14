<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //  学生卡券表
        Schema::create('student_card', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('number_card_id')->default('0')->comment('会员卡记录ID');
            $table->unsignedInteger('student_id')->default('0')->comment('归属学生记录ID');
            $table->integer('card_id')->default('0')->comment('卡券ID');
            $table->integer('number')->default('1')->comment('购买数量');
            $table->decimal('card_price', 10, 2)->default('0')->comment('卡券价格');
            $table->integer('total_class_number')->default('0')->comment('课堂总数');
            $table->integer('residue_class_number')->default('0')->comment('已上课程数');
            $table->dateTime('start_time')->nullable()->commnet('卡券有效期开始时间');
            $table->dateTime('end_time')->nullable()->commnet('卡券有效期结束时间');
            $table->tinyInteger('status')->default('1')->comment('卡券有效状态 0否 1是');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->string('operator_name',60)->default('')->comment('操作用户姓名');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->index(['number_card_id','card_id']);
            $table->index('operator_id');
            $table->comment = '学生卡券购买记录表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_card');
    }
}
