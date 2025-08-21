<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnPackageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_histories', function (Blueprint $table) {
            $table->double('weight_staff')->after('length')->nullable();
            $table->double('width_staff')->after('weight_staff')->nullable();
            $table->double('height_staff')->after('width_staff')->nullable();
            $table->double('length_staff')->after('height_staff')->nullable();
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
            $table->double('weight_staff')->after('length')->nullable();
            $table->double('width_staff')->after('weight_staff')->nullable();
            $table->double('height_staff')->after('width_staff')->nullable();
            $table->double('length_staff')->after('height_staff')->nullable();
        });
    }
}
