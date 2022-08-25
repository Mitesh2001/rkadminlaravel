<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleIdLeadAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_assigns', function (Blueprint $table) {
            $table->integer('role_id')->default(0)->nullable()->after('user_id');
        });
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            DB::statement('ALTER TABLE `tele_caller_contacts` CHANGE `user_id` `user_id` BIGINT(20) UNSIGNED NULL');
            $table->integer('role_id')->default(0)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_assigns', function (Blueprint $table) {
            $table->dropColumn(['role_id']);
        });
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            $table->dropColumn(['role_id']);
        });
    }
}
