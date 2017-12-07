<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperatorNaneCardPrefixToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('venues', function (Blueprint $table) {
            //
            $table->string('operator_name',60)->default('')->after('operator_id')->comment('操作用户姓名');
            $table->string('card_prefix',60)->default('')->after('parent_id')->comment('会员卡前缀');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('venues', function (Blueprint $table) {
             Schema::dropIfExists('venues');
        });
    }
}
