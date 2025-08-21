<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRequestPackageGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_package_groups', function (Blueprint $table) {
            $table->tinyInteger('ship_mode')->after('file')->nullable();

            $table->tinyInteger('is_insurance')->after('ship_mode')->nullable();
            $table->integer('insurance_fee')->after('is_insurance')->nullable(); // raw fee
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_package_groups', function (Blueprint $table) {
            $table->dropColumn(['ship_mode', 'is_insurance', 'insurance_fee']);
        });
    }
}
