<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('totes', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('warehouse_area_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->string('name');
            $table->string('barcode');
            $table->tinyInteger('status');

            $table->softDeletes();
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
        Schema::dropIfExists('totes');
    }
}
