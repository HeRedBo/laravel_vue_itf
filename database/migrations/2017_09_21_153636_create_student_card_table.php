<?php

use Illuminate\Support\Facades\Schema;
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
        //  学生卡券时间
        Schema::create('student_card', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('student_id')->default('0')->comment('学生ID');
            $table->integer('card_id')->default('0')->comment('卡券ID');
            $table->dateTime('start_time')->commnet('卡券有效期开始时间');
            $table->dateTime('end_time')->commnet('卡券有效期结束时间');
            $table->tinyInteger('status')->default('1')->comment('卡券有效状态 0否1是');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamps();
            $table->index(['student_id','card_id']);
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
        Schema::dropIfExists('student_card');
    }
}
