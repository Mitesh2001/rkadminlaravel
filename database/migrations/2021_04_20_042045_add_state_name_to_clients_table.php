<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStateNameToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('state_name')->after('state_id')->nullable();
        });
        Schema::table('company', function (Blueprint $table) {
            $table->string('state_name')->after('state_id')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('state_name')->after('state_id')->nullable();
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
            $table->dropColumn(['state_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['state_id']);
        });
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn(['state_id']);
        });
    }
}
