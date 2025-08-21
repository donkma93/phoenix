<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatBoxDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_box_details', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();

            $table->foreignId('chat_box_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->foreignId('from_user_id')
                ->constrained('users')
                ->onUpdate('cascade');
            $table->longText('message');
            $table->timestamp('read_at')->nullable();
            $table->tinyInteger('user_get')->default(0);
            $table->tinyInteger('staff_get')->default(0);

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
        Schema::dropIfExists('chat_box_details');
    }
}
