<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnToWarehouseAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_areas', function (Blueprint $table) {
            $table->string('barcode')
                ->nullable()
                ->unique()
                ->after('name');
            $table->boolean('is_full')
                ->default(false)
                ->after('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_areas', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'is_full']);
        });
    }
}
