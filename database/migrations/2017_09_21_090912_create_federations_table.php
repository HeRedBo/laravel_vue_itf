<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFederationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('federations', function(Blueprint $table)
        {
            $table->increments('id')->comment('联盟ID');
            $table->string('name', 50)->default('')->comment('联盟名称');
            $table->string('logo',255)->comment('联盟logo');
            $table->string('logo_thumb',255)->comment('联盟logo缩略图');
            $table->string('remark', 255)->comment('联盟备注信息');
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
        Schema::dropIfExists('federations');
    }
}
