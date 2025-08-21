<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnSizeToPackageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_histories', function (Blueprint $table) {
            $table->double('width')->after('weight')->nullable();
            $table->double('height')->after('width')->nullable();
            $table->double('length')->after('height')->nullable();
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
            $table->dropColumn(['width', 'height', 'length']);
        });
    }
}
