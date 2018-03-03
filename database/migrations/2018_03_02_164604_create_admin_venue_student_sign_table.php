<?php
use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminVenueStudentSignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_venue_student_sign', function(Blueprint $table)
        {
            $table->increments('id')->comment('主键ID');
            $table->integer('venue_id')->unsigned()->default('0')->comment('道馆ID');
            $table->integer('student_id')->unsigned()->default('0')->comment('学生ID');
            $table->integer('class_id')->unsigned()->default('0')->comment('班级ID');
            $table->date('sign_date')->comment('签到时间');
            $table->tinyInteger('status')->unsigned()->default('0')->comment('签到状态');
            $table->string('remark', 255)->comment('课程备注信息');
            $table->integer('operator_id')->unsigned()->default('0')->comment('操作人ID');
            $table->dateTime('created_at')->comment('创建时间');
            $table->index(["student_id"]);
            $table->index(["venue_id","class_id"]);
            $table->comment = '后台道馆学生签到表';
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
        Schema::dropIfExists('admin_venue_student_sign');
    }
}
