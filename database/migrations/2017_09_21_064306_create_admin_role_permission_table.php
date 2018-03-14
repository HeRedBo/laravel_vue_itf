<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminRolePermissionTable extends Migration
{
    protected $table_name = 'admin_role_permission';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->integer('permission_id')->unsigned()->comment('权限id');
            $table->integer('role_id')->unsigned()->index('permission_role_role_id_foreign')->comment('角色ID');
            $table->unique(['permission_id','role_id']);
            $table->comment = '角色权限表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
