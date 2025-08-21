<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_details', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('package_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_group_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->integer('unit_number');
            $table->integer('received_unit_number')->nullable();

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
        Schema::dropIfExists('package_details');
    }
}
