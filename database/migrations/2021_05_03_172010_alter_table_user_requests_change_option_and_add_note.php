<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableUserRequestsChangeOptionAndAddNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Doctrine-change method not support `tinyInteger` type
        DB::statement("UPDATE user_requests SET `option` = '0'");
        DB::statement("ALTER TABLE user_requests CHANGE `option` `option` TINYINT NULL DEFAULT NULL;");

        Schema::table('user_requests', function (Blueprint $table) {
            $table->text('note')->nullable()->after('option');
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
            $table->string('option')->change();
            $table->dropColumn('note');
        });
    }
}
