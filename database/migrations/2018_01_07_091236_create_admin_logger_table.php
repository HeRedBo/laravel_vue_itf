<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 管理操作日志表
        Schema::create('admin_logger', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->integer('venue_id')->unsigned()->default(0)->comment('道馆ID');
            $table->string('catalog', 10)->default('')->comment('类型');
            $table->string('url')->default('')->comment('url');
            $table->text('intro', 65535)->nullable()->comment('操作');
            $table->char('ip', 15)->default('127.0.0.1')->comment('操作ip');
            $table->integer('created_at')->unsigned()->default(0)->comment('操作时间');
            $table->comment = '管理操作日志表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
