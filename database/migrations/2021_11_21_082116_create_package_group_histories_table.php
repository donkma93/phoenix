<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageGroupHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_group_histories', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('package_group_id')
                ->constrained()
                ->onUpdate('cascade');
            
            $table->foreignId('previous_user_id')
                ->constrained('users')
                ->onUpdate('cascade');
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade');

            $table->string('previous_name');
            $table->string('name');

            $table->string('previous_barcode')->nullable();
            $table->string('barcode')->nullable();

            $table->string('previous_unit_weight')->nullable();
            $table->string('unit_weight')->nullable();

            $table->string('previous_unit_height')->nullable();
            $table->string('unit_height')->nullable();

            $table->string('previous_unit_length')->nullable();
            $table->string('unit_length')->nullable();

            $table->string('previous_unit_width')->nullable();
            $table->string('unit_width')->nullable();

            $table->string('previous_unit_size')->nullable();
            $table->string('unit_size')->nullable();

            $table->foreignId('staff_id')
                ->constrained('users')
                ->onUpdate('cascade');

            $table->string('stage');
            $table->tinyInteger('type');

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
        Schema::dropIfExists('package_group_histories');
    }
}
