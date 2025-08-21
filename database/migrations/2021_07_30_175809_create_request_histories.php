<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_histories', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('request_package_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->foreignId('staff_id')
                ->constrained('users')
                ->onUpdate('cascade');

            $table->integer('package_number')->nullable();
            $table->integer('unit_number')->nullable();

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
        Schema::dropIfExists('request_histories');
    }
}
