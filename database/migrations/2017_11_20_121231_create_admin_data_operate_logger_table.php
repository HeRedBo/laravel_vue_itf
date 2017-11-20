<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminDataOperateLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('admin_data_operate_logger', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->string('type',60)->default('')->comment('数据类型,自定义的数据类型格式 如 order,card');
            $table->unsignedInteger('type_id')->default('0')->comment('对应的记录ID');
            $table->string('operation',255)->default('')->comment('操作描述');
            $table->string('field',255)->default('')->comment('字段');
            $table->text('oldValue')->comment('旧值');
            $table->text('newValue',10000)->comment('新值');
            $table->timestamp('created_at')->nullable()->comment('操作时间');
            $table->unsignedInteger('operator_id')->default('0')->comment('操作用户ID');
            $table->string('operator_name',60)->default('')->comment('操作用户姓名');
            $table->index(['type','type_id']);
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
         Schema::dropIfExists('admin_data_operate_logger');
    }
}
