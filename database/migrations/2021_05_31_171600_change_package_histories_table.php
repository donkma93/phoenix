<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePackageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_histories', function (Blueprint $table) {
            $table->foreignId('warehouse_area_id')
                ->after('package_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('staff_id')
                ->nullable()
                ->after('warehouse_area_id')
                ->constrained('users')
                ->onUpdate('cascade');

            $table->integer('unit_number')->nullable();
            $table->tinyInteger('previous_status')->after('status');
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
            $table->dropForeign(['warehouse_area_id']);
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['previous_status', 'warehouse_area_id', 'staff_id', 'unit_number']);
        });
    }
}
