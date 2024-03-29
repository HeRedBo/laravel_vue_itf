<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToAdminClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('admin_classes', function (Blueprint $table) {
            $table->tinyInteger('status')->default('1')->after('remark')->comment('课程状态 0：停止 1：启用');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_classes', function (Blueprint $table) {
            //
        });
    }
}
