<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminVenueTable extends Migration
{
    protected $table_name = 'admin_venue';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         // 道馆课程表
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->integer('admin_id')->unsigned()->comment('管理员ID');
            $table->integer('venue_id')->unsigned()->comment('道馆ID');
            $table->unique(['admin_id','venue_id']);
            $table->comment = '管理员道馆关联表';
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
