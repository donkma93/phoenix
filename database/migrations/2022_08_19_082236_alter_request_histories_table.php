<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRequestHistoriesTable extends Migration
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
            $table->foreignId('request_package_id')
                ->nullable()
                ->change();

            $table->foreignId('package_id')
                ->constrained()
                ->onUpdate('cascade')
                ->nullable();
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
        Schema::table('request_histories', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id']);
        });
    }
}
