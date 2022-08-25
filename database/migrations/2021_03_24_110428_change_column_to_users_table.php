<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE `users` CHANGE `state_id` `state_id` INT(10) UNSIGNED NULL DEFAULT '0'");
            DB::statement("ALTER TABLE `users` CHANGE `country_id` `country_id` INT(10) UNSIGNED NULL DEFAULT '0'");
        });
        DB::statement("ALTER TABLE `construction_contacts` CHANGE `house_type` `house_type` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '1=flat,2=office,3=house'");
        Schema::table('construction_contacts', function (Blueprint $table) {
            $table->string('square_feet')->after('flat_selection')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('construction_contacts', function (Blueprint $table) {
            $table->dropColumn(['square_feet']);
        });
    }
}
