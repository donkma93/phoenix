<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContentRequestPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('request_packages', function (Blueprint $table) {
            $table->dropForeign(['user_request_id']);
            $table->dropForeign(['package_group_id']);
            $table->dropColumn(['user_request_id', 'package_group_id', 'tracking_number']);

            $table->foreignId('request_package_group_id')
                ->after('id')
                ->constrained()
                ->onUpdate('cascade');

            $table->integer('unit_number')->nullable()->change();
            $table->integer('received_package_number')->after('package_number')->nullable();
            $table->integer('received_unit_number')->after('unit_number')->nullable();

            $table->double('unit_length')->after('received_unit_number')->nullable();
            $table->double('unit_weight')->after('unit_length')->nullable();
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
        Schema::disableForeignKeyConstraints();

        Schema::table('request_packages', function (Blueprint $table) {
            $table->foreignId('user_request_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_group_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->string('tracking_number')->nullable();
            $table->integer('unit_number')->change();
            $table->dropForeign(['request_package_group_id']);
            $table->dropColumn(['received_package_number', 'received_unit_number', 'request_package_group_id', 'unit_length', 'unit_weight']);
        });

        Schema::enableForeignKeyConstraints();
    }
}
