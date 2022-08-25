<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowUpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_up', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('follow_up_id')->default(0);
            $table->tinyInteger('type')->comment('1=lead,2=contact')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('created_by')->default(0);
            $table->integer('role_id')->default(0);
            $table->index(['follow_up_id','type','user_id','role_id','created_by']);
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('follow_up');
    }
}
