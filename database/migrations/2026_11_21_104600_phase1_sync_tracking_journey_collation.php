<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Phase1SyncTrackingJourneyCollation extends Migration
{
    public function up()
    {
        // Sync collation of order_tracking_journey (and sys_* lookup tables) to utf8mb4_unicode_ci
        // so JOINs with orders.tracking & order_transactions.tracking_number can use B-Tree indexes.
        DB::statement('ALTER TABLE `sys_country` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE `sys_states` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE `sys_cities` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE `order_tracking_journey` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ANALYZE TABLE `order_tracking_journey`');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `order_tracking_journey` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        DB::statement('ALTER TABLE `sys_cities` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        DB::statement('ALTER TABLE `sys_states` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        DB::statement('ALTER TABLE `sys_country` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
    }
}
