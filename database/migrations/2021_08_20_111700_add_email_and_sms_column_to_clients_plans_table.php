<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailAndSmsColumnToClientsPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients_plan', function (Blueprint $table) {
            $table->bigInteger('no_of_sms')->after('plan_id')->default(0);
            $table->bigInteger('no_of_email')->after('no_of_sms')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients_plan', function (Blueprint $table) {
            $table->dropColumn(['no_of_sms','no_of_email']);
        });
    }
}
