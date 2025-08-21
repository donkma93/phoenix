<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestWorkingTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_working_times', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->foreignId('user_request_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('finish_at')->nullable();
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
        Schema::dropIfExists('request_working_times');
    }
}
