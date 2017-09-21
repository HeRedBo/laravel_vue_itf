<?php

use Illuminate\Support\Facades\Schema;
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
            $table->integer('province_code')->unsigned()->default('0')->comment('省份_code');
            $table->integer('city_code')->unsigned()->default('0')->comment('市区_code');
            $table->integer('district_code')->unsigned()->default('0')->comment('区域code');
            $table->string('address',255)->comment('道馆地址');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamps();
            $table->index('operator_id');
            $table->index(['province_code','city_code','district_code']);
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
