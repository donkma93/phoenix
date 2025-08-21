<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnsWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('type')->nullable(); // A|B VN|US
            $table->string('sender_name')->nullable();
            $table->string('sender_street')->nullable();
            $table->string('sender_city')->nullable();
            $table->string('sender_zip')->nullable();
            $table->string('sender_province')->nullable();
            $table->string('sender_country')->nullable();
            $table->string('sender_company')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn(['type']);
            $table->dropColumn(['sender_name']);
            $table->dropColumn(['sender_street']);
            $table->dropColumn(['sender_city']);
            $table->dropColumn(['sender_zip']);
            $table->dropColumn(['sender_province']);
            $table->dropColumn(['sender_country']);
            $table->dropColumn(['sender_company']);
        });
    }
}
