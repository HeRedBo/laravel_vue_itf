<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function(Blueprint $table)
        {
            $table->increments('id')->comment('学生ID');
            $table->char('name', 60)->default('')->comment('学生姓名');
            $table->char('native_place', 60)->default('')->comment('籍贯');
            $table->tinyInteger('sex')->unsigned()->default('1')->comment('性别(0:女,1:男)'); 
            $table->string('picture',255)->comment('头像');
            $table->dateTime('birthday')->commnet('出生年月');
            $table->string('id_card')->default('')->comment('身份证');
            $table->string('school')->default('')->comment('学校');
            $table->integer('province_code')->unsigned()->default('0')->comment('省份_code');
            $table->string('province',100)->default('')->comment('省份');
            $table->integer('city_code')->unsigned()->default('0')->comment('市区_code');
            $table->string('city',100)->default('0')->comment('市区');
            $table->integer('area_code')->unsigned()->default('0')->comment('区域code');
            $table->string('area',100)->default('')->comment('区域code');
            $table->string('address',255)->comment('家庭详细地址');
            $table->dateTime('sign_up_at')->commnet('报名时间');
            $table->integer('venue_id')->unsigned()->comment('道馆ID');
            $table->integer('class_id')->unsigned()->comment('班级ID');
            $table->tinyInteger('status')->default('0')->comment('学生状态 0草稿，1正式');
            $table->timestamps();
            $table->index(['venue_id']);
            $table->index(['province_code', 'city_code','area_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
