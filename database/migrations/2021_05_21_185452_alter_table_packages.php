<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['package_code', 'width', 'height', 'length', 'quantity']);
            $table->renameColumn('code', 'barcode');

            $table->foreignId('user_id')
                ->after('id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_group_id')
                ->after('user_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('warehouse_area_id')
                ->after('package_group_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade');

            $table->tinyInteger('status')->after('package_group_id');
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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('package_code')->after('name');
            $table->double('width')->after('package_code');
            $table->double('height')->after('width');
            $table->double('length')->after('height');
            $table->integer('quantity')->after('length');
            $table->renameColumn('barcode', 'code');
            $table->dropForeign(['user_id']);
            $table->dropForeign(['package_group_id']);
            $table->dropForeign(['warehouse_area_id']);
            $table->dropColumn(['status', 'user_id', 'package_group_id', 'warehouse_area_id']);
        });
    }
}
