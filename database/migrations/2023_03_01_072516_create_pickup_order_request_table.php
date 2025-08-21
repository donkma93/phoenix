<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupOrderRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_order_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId("pickups_id")
            ->references("id")->on("pickups");
            $table->foreignId("order_id")
            ->references("id")->on("orders");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_order_request');
    }
}
