<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPriceUnitPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_prices', function (Blueprint $table) {
            $table->renameColumn('price', 'min_size_price');
            $table->double('max_size_price')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_prices', function (Blueprint $table) {
            $table->renameColumn('min_price', 'price');
            $table->dropColumn(['max_size_price']);
        });
    }
}
