<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenueBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 道馆账单表
        Schema::create('venue_bill', function (Blueprint $table) 
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->unsigned()->default('0')->comment('道馆ID');
            $table->string('bill_no',30)->default('')->comment('账单编号');
            $table->string('bill_title',255)->default('')->comment('账单内容 用户说明账单的基本信息 数据来源等。');
            $table->tinyInteger('bill_type')->default('1')->comment('账单类型 1：入账 2：出账');
            $table->tinyInteger('data_type')->default('1')->comment('账单数据类型 1：卡券 2：考级账单 详情看账单数据类型表');
            $table->integer('student_card_id')->unsigned()->default('0')->comment('学生卡券记录ID 当数据类型为学生卡券是才字段必填');
            $table->decimal('money',10,2)->default('0')->comment('账单金额');
            $table->char('currency',30)->default('cny')->comment('币种');
            $table->tinyInteger('status')->default('0')->comment('账单状态 0：未审核 1：审核失败 2：通过');
            $table->string('remark',255)->comment('账单备注');
            $table->timestamp('bill_created_at')->nullable()->comment('账单创建时间');
            $table->unsignedInteger('create_user_id')->default('0')->comment('账单创建人用户ID');
            $table->unsignedInteger('operator_id')->default('0')->comment('操作用户ID');
            $table->string('operate_remark',255)->comment('账单操作备注');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment = '道馆账单表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venue_bill');
    }
}
