<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_request', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_code')->nullable(false);
            $table->foreignId('id_warehouse')->nullable()->constrained('warehouses');
            $table->integer('status')->nullable(false);
            $table->timestamp('action_date')->nullable();
            $table->timestamp('finish_date')->nullable();
            $table->string('finish_user')->nullable();
            $table->timestamp('created_date')->nullable();
            $table->timestamps();
            $table->foreignId('user_create')->constrained('users')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_request');
    }
}
