<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeInfoRequestPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_packages', function (Blueprint $table) {
            $table->double('width')->after('received_unit_number')->nullable();
            $table->double('weight')->after('width')->nullable();
            $table->double('height')->after('weight')->nullable();
            $table->double('length')->after('height')->nullable();

            $table->dropColumn(['unit_length', 'unit_weight']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_packages', function (Blueprint $table) {
            $table->double('unit_length')->after('received_unit_number')->nullable();
            $table->double('unit_weight')->after('unit_length')->nullable();

            $table->dropColumn(['width', 'weight', 'height', 'length']);
        });
    }
}
