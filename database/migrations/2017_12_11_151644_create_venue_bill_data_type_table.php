<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenueBillDataTypeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 道馆账单数据类型字典表
        Schema::create('venue_bill_data_type', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->unsigned()->default('0')->comment('道馆ID');
            $table->tinyInteger('type')->default('1')->comment('账单数据类型 1支出,2收入');
            $table->char('name', 60)->default('')->comment('账单数据类型名称');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venue_bill_data_type');
    }
}
