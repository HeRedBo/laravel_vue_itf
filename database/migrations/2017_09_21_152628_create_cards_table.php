<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 卡券表
        Schema::create('cards', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->unsignedInteger('venue_id')->default('0')->comment('道馆ID');
            $table->tinyInteger('type')->unsigned()->default('1')->comment('卡类 1期卡 2: 次卡');
            $table->string('name', 255)->default('')->comment('卡券名称');
            $table->integer('number')->default('0')->comment('计算数据 如果卡是期卡 这儿是期卡有效期数量 如果是次卡 该值为次数');
            $table->string('unit',20)->defualt('')->comment('卡券计算单位 day：天 mouth 月 year 年');
            $table->decimal('card_price', 10, 2)->default('0')->comment('卡券价格');
            $table->string('explain',255)->comment('卡券说明');
            $table->unsignedTinyInteger('status')->default('0')->comment('启用状态 0:否1:是');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->index('venue_id');
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
        //
        Schema::dropIfExists('cards');
    }
}
