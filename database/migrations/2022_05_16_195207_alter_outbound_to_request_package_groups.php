<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOutboundToRequestPackageGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_package_groups', function (Blueprint $table) {
            $table->integer('quantity')->after('file')->nullable();
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
            $table->dropColumn(['quantity']);
        });
    }
}
