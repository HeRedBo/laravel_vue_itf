<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCardSnapIdToStudentCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_card', function (Blueprint $table) {
            $table->integer('card_snap_id')->default('0')->after('card_id')->comment('卡券快照ID');
            $table->index(['card_snap_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_card', function (Blueprint $table) {
            //
        });
    }
}
