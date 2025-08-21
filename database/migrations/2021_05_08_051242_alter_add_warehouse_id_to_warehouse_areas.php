<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddWarehouseIdToWarehouseAreas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('warehouse_areas', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                ->after('name')
                ->constrained('warehouses')
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
        Schema::table('warehouse_areas', function (Blueprint $table) {
            $table->dropColumn(['warehouse_id']);
        });
    }
}
