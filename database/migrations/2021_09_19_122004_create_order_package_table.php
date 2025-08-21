<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_package', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->double('width')->nullable();
            $table->double('height')->nullable();
            $table->double('length')->nullable();
            $table->double('weight')->nullable();

            $table->tinyInteger('size_type')->nullable();
            $table->tinyInteger('weight_type')->nullable();

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
        Schema::dropIfExists('order_package');
    }
}
