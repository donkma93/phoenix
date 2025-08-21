<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRemoveColumnUserRequestInToteHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('tote_histories', function (Blueprint $table) {
            $table->dropForeign(['user_request_id']);
            $table->dropColumn('user_request_id');

            $table->foreignId('order_id')
                ->constrained()
                ->onUpdate('cascade');
            $table->timestamp('pick_at')->after('packer_id')->nullable();
            $table->timestamp('pack_at')->after('pick_at')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tote_histories', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn(['order_id', 'pick_at', 'pack_at']);

            $table->foreignId('user_request_id')
            ->constrained()
            ->onUpdate('cascade');
        });
    }
}
