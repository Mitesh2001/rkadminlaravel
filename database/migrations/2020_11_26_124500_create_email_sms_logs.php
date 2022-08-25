<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSmsLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sms_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('template_id')->nullable();
            $table->string('type')->nullable(); // EMAIL OR SMS
            $table->text('response')->nullable(); // Response
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
		Schema::table('email_sms_logs', function (Blueprint $table) {
            $table->dropForeign('email_sms_logs_user_id_foreign');
        });
        Schema::dropIfExists('email_sms_logs');
    }
}
