<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToteHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tote_histories', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('user_request_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->foreignId('product_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->integer('quantity');
            $table->foreignId('picker_id')->nullable()->constrained('users');
            $table->foreignId('packer_id')->nullable()->constrained('users');

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
        Schema::dropIfExists('tote_histories');
    }
}
