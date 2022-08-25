<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('email_template_id')->defualt(0);
            $table->tinyInteger('type')->comment('1=lead,2=contact')->defualt(0);
            $table->bigInteger('type_id')->default(0);
            $table->bigInteger('sender_id')->default(0);
            $table->bigInteger('receiver_id')->default(0);
            $table->string('receiver_email')->nullable();
            $table->tinyInteger('is_send')->comment('1=yes,0=no')->defualt(0);
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
        Schema::dropIfExists('email_history');
    }
}
