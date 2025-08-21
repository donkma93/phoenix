<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPricingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('pricing_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->longText('note')->nullable()->change();
            $table->string('email')->after('user_id')->nullable();
            $table->string('company')->after('email')->nullable();
            $table->string('name')->after('company')->nullable();
            $table->string('phone')->after('name')->nullable();
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
        Schema::disableForeignKeyConstraints();

        Schema::table('pricing_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->longText('note')->nullable(false)->change();
            $table->dropColumn(['email', 'company', 'name', 'phone']);
        });

        Schema::enableForeignKeyConstraints();
    }
}
