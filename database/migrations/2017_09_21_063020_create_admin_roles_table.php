<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminRolesTable extends Migration
{
    protected $table_name = 'admin_roles';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id',11)->comment('主键ID');
            $table->string('name', 50)->unique('roles_name_unique')->comment('角色名称');
            $table->string('display_name', 50)->default('')->comment('显示名称');
            $table->string('description', 100)->default('')->comment('角色描述');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE $this->table_name comment '用户角色关联表'");
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
