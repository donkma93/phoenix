<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string('name');
            $table->tinyInteger('status');

            $table->double('fulfillment_fee')->nullable();
            $table->double('extra_pick_fee')->nullable();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_group_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('user_id')
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
        Schema::dropIfExists('products');
    }
}
