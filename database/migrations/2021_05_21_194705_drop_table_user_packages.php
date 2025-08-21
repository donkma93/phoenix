<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableUserPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_packages');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('user_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('warehouse_area_id')->nullable()
                ->constrained()
                ->onUpdate('cascade');

            $table->tinyInteger('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
