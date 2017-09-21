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
            $table->tinyInteger('sex')->unsigned()->default('1')->comment('性别(0:女,1:男)'); 
            $table->dateTime('birthday')->commnet('出生年月');
            $table->integer('province_code')->unsigned()->default('0')->comment('省份_code');
            $table->integer('city_code')->unsigned()->default('0')->comment('市区_code');
            $table->integer('district_code')->unsigned()->default('0')->comment('区域code');
            $table->string('address',255)->comment('家庭详细地址');
            $table->integer('venue_id')->unsigned()->comment('道馆ID');
            $table->integer('class_id')->unsigned()->comment('班级ID');
            $table->timestamps();
            $table->index(['venue_id','class_id']);
            $table->index(['province_code', 'city_code','district_code']);
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
