<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_journey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('tracking_label')->nullable();
            $table->foreignId('id_pickup_request')->nullable()->constrained('pickup_request');
            $table->integer('status')->nullable(false);
            $table->string('inout_type')->nullable(false);
            $table->foreignId('id_packing_list')->nullable()->constrained('packing_list');
            $table->foreignId('user_create')->constrained('users')->nullable();
            $table->timestamp('created_date')->nullable();
            $table->string('from_warehouse')->nullable();
            $table->string('to_warehouse')->nullable();
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
        Schema::dropIfExists('order_journey');
    }
}
