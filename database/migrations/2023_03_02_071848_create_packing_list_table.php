<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_list', function (Blueprint $table) {
            $table->id();
            $table->string('packing_list_code')->nullable(false);
            $table->string('master_bill')->nullable();
            $table->integer('status');
            $table->integer('weight')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('finish_date')->nullable();
            $table->string('finish_user')->nullable()->foreignId('user_id')->constrained('users');
            $table->timestamp('created_date');
            $table->string('create_user')->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->string('from_warehouse')->nullable();
            $table->string('to_warehouse')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing_list');
    }
}
