<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
            ->constrained()
            ->onUpdate('cascade');
            $table->foreignId('m_request_type_id')
            ->constrained()
            ->onUpdate('cascade');
            $table->string('option');
            $table->tinyInteger('status');
            $table->string('file');
            $table->string('packages');
            $table->foreignId('staff_id')->nullable()->constrained('users');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('finish_at')->nullable();
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
        Schema::dropIfExists('user_requests');
    }
}
