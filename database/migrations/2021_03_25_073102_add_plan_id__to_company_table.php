<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlanIdToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['plan_id','plan_price','no_of_users']);
        });
        Schema::table('company', function (Blueprint $table) {
            $table->bigInteger('plan_id')->unsigned()->after('client_id')->nullable();
            $table->bigInteger('plan_price')->nullable()->after('plan_id');
            $table->integer('no_of_users')->nullable()->after('plan_price');
        });
        Schema::table('clients_plan', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->after('client_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->bigInteger('plan_id')->unsigned();
            $table->bigInteger('plan_price')->nullable();
            $table->integer('no_of_users')->nullable();
        });

        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn(['plan_id','plan_price','no_of_users']);
        });
        Schema::table('clients_plan', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
    }
}
