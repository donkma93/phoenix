<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->dateTime('date')->nullable();

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

            $table->integer('item_quantity')->nullable();
            $table->string('item_name')->nullable();
            $table->double('item_price')->nullable();
            $table->double('item_compare_at_price')->nullable();
            $table->string('item_sku')->nullable();
            $table->integer('item_requires_shipping')->nullable();
            $table->double('item_taxable')->nullable();
            $table->string('item_fulfillment_status')->nullable();

            $table->tinyInteger('payment');
            $table->tinyInteger('fulfillment');

            // $table->double('fee')->nullable();

            $table->double('ship_rate')->nullable();
            $table->string('tracking')->nullable();
            $table->tinyInteger('status');

            $table->json('content')->nullable();
            $table->string('file')->nullable();

            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade');

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
        Schema::dropIfExists('orders');
    }
}
