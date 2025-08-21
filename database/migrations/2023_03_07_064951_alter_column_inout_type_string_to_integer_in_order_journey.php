<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnInoutTypeStringToIntegerInOrderJourney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_journey', function (Blueprint $table) {
            $table->integer('inout_type')->default(1)->change();
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
        Schema::table('order_journey', function (Blueprint $table) {
            $table->integer('order_id');
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
            $table->string('inout_type')->nullable(false)->change();
            $table->foreignId('order_id')->constrained('orders');
        });
    }
}
