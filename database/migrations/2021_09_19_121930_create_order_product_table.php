<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('product_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->integer('quantity');
            $table->double('total_fee')->nullable();

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
        Schema::dropIfExists('order_product');
    }
}
