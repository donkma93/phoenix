<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRenameColumnInPackageGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_groups', function (Blueprint $table) {
            $table->renameColumn('length', 'unit_length');
            $table->renameColumn('height', 'unit_height');
            $table->renameColumn('width', 'unit_width');
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
            $table->renameColumn('unit_length', 'length');
            $table->renameColumn('unit_height', 'height');
            $table->renameColumn('unit_width', 'width');
        });
    }
}
