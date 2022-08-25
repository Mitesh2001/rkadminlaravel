<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowUpAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_up_assigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('follow_up_id')->default(0)->nullable();
            $table->integer('role_id')->default(0)->nullable();
            $table->bigInteger('user_id')->default(0)->nullable();
            $table->bigInteger('created_by')->default(0)->nullable();
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
        Schema::dropIfExists('follow_up_assigns');
    }
}
