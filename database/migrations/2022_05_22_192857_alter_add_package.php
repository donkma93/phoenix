<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::disableForeignKeyConstraints();

        Schema::table('user_requests', function (Blueprint $table) {
            $table->foreignId('address_from_id')
                ->nullable()
                ->after('id')
                ->constrained('order_addresses')
                ->onUpdate('cascade');

            $table->foreignId('address_to_id')
                ->nullable()
                ->after('address_from_id')
                ->constrained('order_addresses')
                ->onUpdate('cascade');

            $table->tinyInteger('packing_type')->after('address_to_id')->nullable();
            $table->tinyInteger('prep_type')->after('packing_type')->nullable();
            $table->tinyInteger('label_by_type')->after('prep_type')->nullable();
            $table->tinyInteger('store_type')->after('label_by_type')->nullable();
            $table->dateTime('ship_coming')->after('store_type')->nullable();
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
        Schema::table('user_requests', function (Blueprint $table) {
            $table->dropForeign(['address_from_id']);
            $table->dropForeign(['address_to_id']);
            $table->dropColumn(['address_from_id', 'address_to_id', 'packing_type', 'prep_type', 'label_by_type', 'store_type', 'ship_coming']);
        });
    }
}
