<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPackageGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_package_groups', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('user_request_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->foreignId('package_group_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->string('barcode')
                ->nullable()
                ->unique();

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
        Schema::dropIfExists('request_package_groups');
    }
}
