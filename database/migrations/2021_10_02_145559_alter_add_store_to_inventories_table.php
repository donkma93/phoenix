<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddStoreToInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('store_fulfill_id')
                ->after('product_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['store_fulfill_id']);
            $table->dropColumn(['store_fulfill_id']);
        });
    }
}
