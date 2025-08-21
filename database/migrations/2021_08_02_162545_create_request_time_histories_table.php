<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestTimeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_time_histories', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('user_request_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->double('hour');

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
        Schema::dropIfExists('request_time_histories');
    }
}
