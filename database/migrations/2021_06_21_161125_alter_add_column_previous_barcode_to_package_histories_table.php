<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnPreviousBarcodeToPackageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_histories', function (Blueprint $table) {
            $table->string('previous_barcode')->after('barcode');
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
            $table->dropColumn('previous_barcode');
        });
    }
}
