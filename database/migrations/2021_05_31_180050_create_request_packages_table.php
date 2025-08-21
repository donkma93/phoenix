<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_request_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_group_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->integer('package_number')->nullable();
            $table->integer('unit_number');
            $table->string('tracking_number')->nullable();
            $table->string('barcode')->nullable();

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
        Schema::dropIfExists('request_packages');
    }
}
