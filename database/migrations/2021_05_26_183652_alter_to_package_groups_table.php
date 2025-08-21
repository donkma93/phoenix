<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterToPackageGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('package_groups', function (Blueprint $table) {
            $table->string('barcode')->nullable()->change();
            $table->string('file')->after('barcode')->nullable();

            $table->foreignId('user_id')
                ->after('id')
                ->constrained()
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
        Schema::table('package_groups', function (Blueprint $table) {
            $table->string('barcode')->change();
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'file']);
        });
    }
}
