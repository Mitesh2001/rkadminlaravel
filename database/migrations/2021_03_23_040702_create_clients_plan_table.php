<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_plan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('plan_id')->unsigned();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->bigInteger('plan_id')->unsigned()->after('established_in')->nullable();
            $table->bigInteger('plan_price')->nullable()->after('plan_id');
            $table->integer('no_of_users')->nullable()->after('plan_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients_plan');
        
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['plan_id','plan_price','no_of_users']);
        });
    }
}
