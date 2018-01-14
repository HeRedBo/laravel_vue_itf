<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 场馆表
        Schema::create('venues', function(Blueprint $table)
        {
            $table->increments('id')->comment('场馆ID');
            $table->string('name', 50)->default('')->comment('道馆名称');
            $table->integer('federation_id')->default('0')->comment('联盟ID');
            $table->string('logo',255)->comment('道馆logo');
            $table->string('logo_thumb',255)->comment('道馆logo缩略图');
            $table->integer('parent_id')->unsigned()->default(0)->comment('父级ID');
            $table->integer('province_code')->unsigned()->default('0')->comment('省份_code');
            $table->string('province',100)->default('')->comment('省');
            $table->integer('city_code')->unsigned()->default('0')->comment('市区_code');
            $table->string('city',100)->default('')->comment('市');
            $table->integer('area_code')->unsigned()->default('0')->comment('区域code');
            $table->string('area',100)->default('')->comment('区');
            $table->string('address',255)->comment('道馆地址');
            $table->string('remark', 255)->comment('道馆备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamps();
            $table->index('operator_id');
            $table->index(['province_code','city_code','area_code']);
            $table->comment = '道馆信息表';
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venues');
    }
}
