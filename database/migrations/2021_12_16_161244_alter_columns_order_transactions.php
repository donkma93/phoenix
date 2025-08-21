<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsOrderTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('order_transactions', function (Blueprint $table) {
            $table->dropColumn(['rate_id']);

            $table->foreignId('order_rate_id')
                ->nullable()
                ->after('order_id')
                ->constrained()
                ->onUpdate('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->dropForeign(['order_rate_id']);
            $table->dropColumn(['order_rate_id']);
            $table->string('rate_id');
        });
    }
}
