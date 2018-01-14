<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminDataOperateFieldMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 后台数据字段操作字典表
        Schema::create('admin_data_operate_field_map', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('operate_logger_id')->default('0')->comment('操作日志记录表的ID');
            $table->string('field',255)->default('')->comment('字段');
            $table->string('oldValue',255)->default('')->comment('旧值');
            $table->string('newValue',255)->default('')->comment('新值');
            $table->comment = '后台数据操作日志字段修改记录表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_data_operate_field_map');
    }
}
