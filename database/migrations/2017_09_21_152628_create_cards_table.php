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
            $table->string('name', 100)->default('')->comment('卡券名称');
            $table->integer('number')->default('0')->comment('计算单位');
            $table->string('unit',20)->comment('卡券计算单位 day：天 mouth 月 year 年');
            $table->string('explain',255)->comment('卡券说明');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamps();
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
