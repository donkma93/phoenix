<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseLaborToUserInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_invoices', function (Blueprint $table) {
            $table->double('warehouse_labor')->nullable()->after('storage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_invoices', function (Blueprint $table) {
            $table->dropColumn(['warehouse_labor']);
        });
    }
}
