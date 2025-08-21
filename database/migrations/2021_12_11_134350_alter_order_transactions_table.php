<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class AlterOrderTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        Schema::table('order_transactions', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->string('rate_id')->nullable()->change();
            $table->string('transaction_id')->nullable()->change();

            $table->text('label_url')->nullable()->change();
            $table->string('tracking_number')->nullable()->change();
            $table->string('tracking_status')->nullable()->change();
            $table->text('tracking_url_provider')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        Schema::table('order_transactions', function (Blueprint $table) {
            $table->double('amount')->nullable(false)->change();
            $table->string('currency')->nullable(false)->change();
            $table->string('rate_id')->nullable(false)->change();
            $table->string('transaction_id')->nullable(false)->change();

            $table->text('label_url')->nullable(false)->change();
            $table->string('tracking_number')->nullable(false)->change();
            $table->string('tracking_status')->nullable(false)->change();
            $table->text('tracking_url_provider')->nullable(false)->change();
        });
    }
}
