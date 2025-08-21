<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnUnitWeightForPackageGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_groups', function (Blueprint $table) {
            $table->double('unit_weight')->after('unit_width')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_groups', function (Blueprint $table) {
            $table->dropColumn(['unit_weight']);
        });
    }
}
