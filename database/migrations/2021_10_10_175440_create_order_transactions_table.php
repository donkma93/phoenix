<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->string('shipping_name')->nullable();
            $table->string('shipping_street')->nullable();
            $table->string('shipping_address1')->nullable();
            $table->string('shipping_address2')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_province')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_phone')->nullable();

            $table->double('amount');
            $table->string('currency');
            $table->string('rate_id');
            $table->string('transaction_id');

            $table->text('label_url');
            $table->string('tracking_number');
            $table->enum('tracking_status', ['UNKNOWN', 'DELIVERED', 'TRANSIT', 'FAILURE', 'RETURNED']);
            $table->text('tracking_url_provider');

            // content store API response
            // $table->json('content');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_transactions');
    }
}
