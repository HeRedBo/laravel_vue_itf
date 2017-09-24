<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenueScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 道馆课程表
        Schema::create('venue_schedule', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->unsigned()->comment('道馆ID');
            $table->integer('schedule_id')->unsigned()->comment('课程表ID');
            $table->dateTime('start_time')->commnet('课程有效期开始时间');
            $table->dateTime('end_time')->commnet('课程效期结束时间');
            $table->unique(['venue_id','schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venue_schedule');
    }
}
