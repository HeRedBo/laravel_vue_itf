<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    protected $table_name = 'admin';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->string('username',60)->unique()->comment('登录名');
            $table->string('name',100)->comment('昵称');
            $table->string('picture',255)->comment('管理员头像');
            $table->string('email',50)->comment('管理员邮箱');
            $table->string('phone',15)->comment('管理员手机号码');
            $table->string('password',255)->comment('登录密码');
            $table->rememberToken();
            $table->timestamps();
            $table->comment = '后台管理员信息表';
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
