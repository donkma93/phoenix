<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPackageTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_package_trackings', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->foreignId('request_package_group_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->text('tracking_url');
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
        Schema::dropIfExists('request_package_trackings');
    }
}
