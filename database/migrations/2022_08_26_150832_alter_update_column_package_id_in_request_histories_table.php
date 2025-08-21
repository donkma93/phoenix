<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUpdateColumnPackageIdInRequestHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('request_histories', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->change();
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

        Schema::table('request_histories', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable(false)->change();
        });

        Schema::enableForeignKeyConstraints();
    }
}
