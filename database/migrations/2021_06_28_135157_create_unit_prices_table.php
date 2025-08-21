<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_prices', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('m_request_type_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->integer('min_unit')->nullable();
            $table->integer('max_unit')->nullable();
            $table->integer('hour')->nullable();
            $table->double('weight')->nullable();
            $table->double('length')->nullable();
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
        Schema::dropIfExists('unit_prices');
    }
}