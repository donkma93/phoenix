<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_rates', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->boolean('is_active');

            $table->string('object_id');
            $table->string('object_owner');
            $table->string('shipment');
            $table->json('attributes')->nullable();
            $table->string('amount');
            $table->string('currency');
            $table->string('amount_local');
            $table->string('currency_local');
            $table->string('provider');

            $table->string('provider_image_75');
            $table->string('provider_image_200');

            $table->string('service_name');
            $table->json('messages')->nullable();

            $table->integer('estimated_days');
            $table->string('duration_terms');

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
        Schema::dropIfExists('order_rates');
    }
}
