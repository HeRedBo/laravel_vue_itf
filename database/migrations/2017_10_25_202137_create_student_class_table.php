<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_class', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('student_id')->unsigned()->comment('学生记录ID');
            $table->integer('class_id')->unsigned()->comment('班级ID');
            $table->unique(['student_id','class_id']);
            $table->comment = '后台学生班级关联表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_class');
    }
}
