<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_classes', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->default('0')->comment('道馆ID');
            $table->string('name', 50)->default('')->comment('班级名称');
            $table->string('remark', 255)->comment('班级备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->timestamps();
            $table->index('name');
            $table->index('operator_id');
            $table->comment = '班级表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_classes');
    }
}
