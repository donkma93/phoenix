<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->string('packages')->nullable()->change();
            $table->dropColumn('total_package');
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
            $table->string('packages')->change();
            $table->integer('total_package')
                ->nullable()
                ->after('packages');
        });
    }
}
