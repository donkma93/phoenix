<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMUnitPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_unit_id')
            ->constrained()
            ->onUpdate('cascade');
            $table->string('name');
            $table->double('price');
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
        Schema::dropIfExists('m_unit_prices');
    }
}
