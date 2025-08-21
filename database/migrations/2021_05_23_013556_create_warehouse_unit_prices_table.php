<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseUnitPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price');
            $table->foreignId('m_unit_id')
            ->constrained()
            ->onUpdate('cascade');
            $table->foreignId('warehouse_id')
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
        Schema::dropIfExists('warehouse_unit_prices');
    }
}
