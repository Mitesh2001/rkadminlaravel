<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('module_id')->nullable();
            $table->string('module')->nullable()->default(null);
            $table->string('action')->nullable()->default(null);
            $table->text('oldData')->nullable()->default(null);
            $table->text('newData')->nullable()->default(null);

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
		Schema::table('action_logs', function (Blueprint $table) {
            $table->dropForeign('action_logs_user_id_foreign');
        });
        Schema::dropIfExists('action_logs');
    }
}
