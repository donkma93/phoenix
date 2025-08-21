<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->string('object_id')->nullable();

            $table->string('city');
            $table->string('company')->nullable();
            $table->string('country');
            $table->string('email')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('state');
            $table->string('street1');
            $table->string('street2')->nullable();
            $table->string('street3')->nullable();
            $table->string('street_no')->nullable();
            $table->string('zip');

            $table->boolean('is_residential')->default(0);

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
        Schema::dropIfExists('order_addresses');
    }
}
