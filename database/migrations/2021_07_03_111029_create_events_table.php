<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->tinyInteger('type')->defualt(0)->nullable()->comment('1=normal,2=important');
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->bigInteger('user_id')->defualt(0)->nullable();
            $table->bigInteger('created_by')->defualt(0)->nullable();
            $table->bigInteger('updated_by')->defualt(0)->nullable();
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
        Schema::dropIfExists('events');
    }
}
