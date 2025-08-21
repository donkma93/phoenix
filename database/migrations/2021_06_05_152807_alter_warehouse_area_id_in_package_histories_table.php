<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWarehouseAreaIdInPackageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_histories', function (Blueprint $table) {
            $table->foreignId('warehouse_area_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_histories', function (Blueprint $table) {
            $table->foreignId('warehouse_area_id')->change();
        });
    }
}
