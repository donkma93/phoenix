<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kit_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
            ->constrained()
            ->onUpdate('cascade');
            $table->integer('quantity');
            $table->integer('on_hand')->nullable();
            $table->foreignId('component_id')->nullable()->constrained('products');

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
        Schema::dropIfExists('kit_components');
    }
}
