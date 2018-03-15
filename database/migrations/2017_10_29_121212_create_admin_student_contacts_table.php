<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminStudentContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_student_contacts', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('student_id')->unsigned()->comment('学生ID');
            $table->integer('relation_id')->unsigned()->comment('关联联系人ID');
            $table->char('contact_name', 60)->default('')->comment('联系人姓名');
            $table->string('contact_phone',15)->comment('联系人手机号码');
            $table->string('contact_email',60)->comment('联系人邮箱');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->index(['relation_id']);
            $table->comment = '学生联系信息表';
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_student_contacts');
    }
}
