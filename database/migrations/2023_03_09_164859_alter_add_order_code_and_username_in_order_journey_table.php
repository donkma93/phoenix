<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddOrderCodeAndUsernameInOrderJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_journey', function (Blueprint $table) {
            $table->string('order_code')->nullable();
            $table->string('created_username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_journey', function (Blueprint $table) {
            $table->dropColumn('order_code');
            $table->dropColumn('created_username');
        });
    }
}
