<?php

use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartTimeEndTimeAdminCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_cards', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable()->after('status')->comment('卡券有效期开始时间');
            $table->timestamp('end_time')->nullable()->after('start_time')->comment('卡券有效期结束时间');
            $table->index(['start_time','end_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_cards', function (Blueprint $table) {
            //
        });
    }
}
