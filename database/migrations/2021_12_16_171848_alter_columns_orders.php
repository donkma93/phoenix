<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('order_address_from_id')
                ->nullable()
                ->after('id')
                ->constrained('order_addresses')
                ->onUpdate('cascade');

            $table->foreignId('order_address_to_id')
                ->after('order_address_from_id')
                ->constrained('order_addresses')
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_address_from_id']);
            $table->dropForeign(['order_address_to_id']);
            $table->dropColumn(['order_address_from_id', 'order_address_to_id']);
        });
    }
}
