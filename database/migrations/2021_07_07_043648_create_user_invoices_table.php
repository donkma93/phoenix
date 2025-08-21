<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_invoices', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->string('file');
            $table->double('inbound')->nullable();
            $table->double('outbound')->nullable();
            $table->double('relabel')->nullable();
            $table->double('repack')->nullable();
            $table->double('removal')->nullable();
            $table->double('return')->nullable();
            $table->double('storage')->nullable();
            $table->double('balance')->nullable();
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
        Schema::dropIfExists('user_invoices');
    }
}
