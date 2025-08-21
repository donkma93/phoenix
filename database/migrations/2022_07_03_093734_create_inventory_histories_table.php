<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->id();
            $table->foreignId('inventory_id')
            ->constrained()
            ->onUpdate('cascade');
            $table->foreignId('user_id')
            ->constrained()
            ->onUpdate('cascade');

            $table->integer('hour');

            $table->integer('incoming');
            $table->integer('available');
            $table->integer('previous_incoming');
            $table->integer('previous_available');

            $table->timestamp('start_at')->nullable();

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
        Schema::dropIfExists('inventory_histories');
    }
}
