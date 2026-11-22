<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Phase1DatabasePerformanceIndexes extends Migration
{
    protected function indexExists(string $table, string $indexName): bool
    {
        $schema = DB::connection()->getDatabaseName();
        $row = DB::selectOne(
            'SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$schema, $table, $indexName]
        );
        return ((int) ($row->cnt ?? 0)) > 0;
    }

    protected function addIndexIfMissing(string $table, string $indexName, string $columnsSql): void
    {
        if (!$this->indexExists($table, $indexName)) {
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columnsSql})");
        }
    }

    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
        }
    }

    public function up()
    {
        // 1. orders — core search & filter indexes
        $this->addIndexIfMissing('orders', 'idx_orders_order_code', '`order_code`');
        $this->addIndexIfMissing('orders', 'idx_orders_order_number', '`order_number`');
        $this->addIndexIfMissing('orders', 'idx_orders_tracking', '`tracking`');
        $this->addIndexIfMissing('orders', 'idx_orders_partner_code', '`partner_code`');
        $this->addIndexIfMissing('orders', 'idx_orders_deleted_status_created', '`deleted_at`, `status`, `created_at`');
        $this->addIndexIfMissing('orders', 'idx_orders_user_deleted_created', '`user_id`, `deleted_at`, `created_at`');
        $this->addIndexIfMissing('orders', 'idx_orders_picking_created', '`picking_status`, `created_at`');

        // 2. order_transactions — tracking & transaction lookup indexes
        $this->addIndexIfMissing('order_transactions', 'idx_order_tx_tracking_number', '`tracking_number`');
        $this->addIndexIfMissing('order_transactions', 'idx_order_tx_transaction_id', '`transaction_id`');
        $this->addIndexIfMissing('order_transactions', 'idx_order_tx_order_deleted', '`order_id`, `deleted_at`');

        // 3. order_rates — active rate lookup composite index
        $this->addIndexIfMissing('order_rates', 'idx_order_rates_order_active', '`order_id`, `is_active`, `deleted_at`');

        // 4. order_journey — tracking & order code lookup
        $this->addIndexIfMissing('order_journey', 'idx_order_journey_tracking_label', '`tracking_label`');
        $this->addIndexIfMissing('order_journey', 'idx_order_journey_order_code', '`order_code`');
        $this->addIndexIfMissing('order_journey', 'idx_order_journey_status_created', '`status`, `created_at`');

        // 5. sys_country, sys_states, sys_cities — add missing PRIMARY KEYs & foreign lookup indexes
        if (!$this->indexExists('sys_country', 'PRIMARY')) {
            DB::statement('ALTER TABLE `sys_country` MODIFY `id` smallint(5) unsigned NOT NULL, ADD PRIMARY KEY (`id`)');
        }
        $this->addIndexIfMissing('sys_country', 'idx_sys_country_code', '`code`');

        if (!$this->indexExists('sys_states', 'PRIMARY')) {
            DB::statement('ALTER TABLE `sys_states` MODIFY `id` int(10) unsigned NOT NULL, ADD PRIMARY KEY (`id`)');
        }
        $this->addIndexIfMissing('sys_states', 'idx_sys_states_country_id', '`country_id`');

        if (!$this->indexExists('sys_cities', 'PRIMARY')) {
            DB::statement('ALTER TABLE `sys_cities` MODIFY `id` int(11) NOT NULL, ADD PRIMARY KEY (`id`)');
        }
        $this->addIndexIfMissing('sys_cities', 'idx_sys_cities_state_id', '`id_state`');
        $this->addIndexIfMissing('sys_cities', 'idx_sys_cities_country_id', '`id_country`');

        // 6. m_taxes — convert MyISAM -> InnoDB
        DB::statement('ALTER TABLE `m_taxes` ENGINE=InnoDB');

        // 7. order_tracking_journey — drop redundant prefix index (already covered by idx_bill_code_date_journey)
        $this->dropIndexIfExists('order_tracking_journey', 'order_tracking_journey_bill_code_ref_IDX');
    }

    public function down()
    {
        $this->addIndexIfMissing('order_tracking_journey', 'order_tracking_journey_bill_code_ref_IDX', '`bill_code_ref`');

        $this->dropIndexIfExists('sys_cities', 'idx_sys_cities_country_id');
        $this->dropIndexIfExists('sys_cities', 'idx_sys_cities_state_id');
        $this->dropIndexIfExists('sys_states', 'idx_sys_states_country_id');
        $this->dropIndexIfExists('sys_country', 'idx_sys_country_code');

        $this->dropIndexIfExists('order_journey', 'idx_order_journey_status_created');
        $this->dropIndexIfExists('order_journey', 'idx_order_journey_order_code');
        $this->dropIndexIfExists('order_journey', 'idx_order_journey_tracking_label');

        $this->dropIndexIfExists('order_rates', 'idx_order_rates_order_active');

        $this->dropIndexIfExists('order_transactions', 'idx_order_tx_order_deleted');
        $this->dropIndexIfExists('order_transactions', 'idx_order_tx_transaction_id');
        $this->dropIndexIfExists('order_transactions', 'idx_order_tx_tracking_number');

        $this->dropIndexIfExists('orders', 'idx_orders_picking_created');
        $this->dropIndexIfExists('orders', 'idx_orders_user_deleted_created');
        $this->dropIndexIfExists('orders', 'idx_orders_deleted_status_created');
        $this->dropIndexIfExists('orders', 'idx_orders_partner_code');
        $this->dropIndexIfExists('orders', 'idx_orders_tracking');
        $this->dropIndexIfExists('orders', 'idx_orders_order_number');
        $this->dropIndexIfExists('orders', 'idx_orders_order_code');
    }
}
