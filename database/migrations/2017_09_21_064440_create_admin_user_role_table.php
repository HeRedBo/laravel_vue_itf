<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_role', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('user_id')->unsigned()->comment('管理员ID');
            $table->integer('role_id')->unsigned()->index('role_user_role_id_foreign')->comment('角色ID');
            $table->unique(['user_id','role_id']);
            $table->comment = '用户角色关联表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_role');
    }
}
