<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOutboundToUserRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->tinyInteger('ship_mode')->after('status')->nullable();
            $table->integer('is_insurance')->after('ship_mode')->nullable();
            $table->integer('is_allow')->after('is_insurance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->dropColumn(['ship_mode', 'is_insurance', 'is_allow']);
        });
    }
}
