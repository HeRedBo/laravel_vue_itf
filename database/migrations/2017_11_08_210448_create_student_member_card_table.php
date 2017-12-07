<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  学生会员卡表
 */
class CreateStudentMemberCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_number_card', function(Blueprint $table)
        {

            $table->increments('id')->comment('主键ID');
            $table->string('number',30)->default('')->comment('会员卡编号');
            $table->unsignedInteger('student_id')->default('0')->comment('学生ID');
            $table->unsignedTinyInteger('status')->default('0')->comment('状态 0停用 1启用');
            $table->unsignedInteger('operator_id')->default('0')->comment('操作用户ID');
            $table->string('operator_name',60)->default('')->comment('操作用户姓名');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->index('student_id');
            $table->index(['number','status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('student_number_card');
    }
}
