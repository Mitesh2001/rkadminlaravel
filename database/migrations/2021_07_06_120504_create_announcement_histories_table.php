<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('announcement_id')->default(0)->nullable();
            $table->bigInteger('user_id')->default(0)->nullable();
            $table->index(['announcement_id','user_id']);
            $table->timestamps();
        });
        Schema::table('events', function (Blueprint $table) {
            DB::statement('ALTER TABLE `events` CHANGE `user_id` `user_id` VARCHAR(199) NULL DEFAULT NULL');
            $table->text('description')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_histories');
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }
}
