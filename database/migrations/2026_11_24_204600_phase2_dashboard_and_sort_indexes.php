<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Phase2DashboardAndSortIndexes extends Migration
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
        // Speeds up StaffDashboard & AdminDashboard fulfillment GROUP BY / PENDING SUM queries
        $this->addIndexIfMissing('orders', 'idx_orders_deleted_fulfillment', '`deleted_at`, `fulfillment`');

        // Speeds up ORDER BY updated_at DESC in AdminOrderService::list & UserOrderService::index
        $this->addIndexIfMissing('orders', 'idx_orders_deleted_updated', '`deleted_at`, `updated_at`');
        $this->addIndexIfMissing('orders', 'idx_orders_user_deleted_updated', '`user_id`, `deleted_at`, `updated_at`');

        // Covering index for SUM(quantity) joins on order_product
        $this->addIndexIfMissing('order_product', 'idx_order_product_order_deleted_qty', '`order_id`, `deleted_at`, `quantity`');
    }

    public function down()
    {
        $this->dropIndexIfExists('order_product', 'idx_order_product_order_deleted_qty');
        $this->dropIndexIfExists('orders', 'idx_orders_user_deleted_updated');
        $this->dropIndexIfExists('orders', 'idx_orders_deleted_updated');
        $this->dropIndexIfExists('orders', 'idx_orders_deleted_fulfillment');
    }
}
