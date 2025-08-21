<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangePackageGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_groups', function (Blueprint $table) {
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN `width` DOUBLE NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN `height` DOUBLE NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN `length` DOUBLE NULL DEFAULT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_groups', function (Blueprint $table) {
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN `width` DOUBLE NOT NULL DEFAULT 0;");
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN `height` DOUBLE NOT NULL DEFAULT 0;");
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN `length` DOUBLE NOT NULL DEFAULT 0;");
        });
    }
}
