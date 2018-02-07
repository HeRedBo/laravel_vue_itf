<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminVenueScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 道馆课程表
        Schema::create('admin_venue_schedule', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->unsigned()->comment('道馆ID');
            $table->integer('course_count')->unsigned()->comment('道馆每日课程数');
            $table->dateTime('start_time')->commnet('课程有效期开始时间');
            $table->dateTime('end_time')->commnet('课程效期结束时间');
            $table->tinyInteger('status')->default('0')->comment('启用状态 1:启用 0:未启用');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->index('operator_id');
            $table->index(['venue_id']);
            $table->index(['start_time','end_time']);
            $table->comment = '道馆课程表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_venue_schedule');
    }
}
