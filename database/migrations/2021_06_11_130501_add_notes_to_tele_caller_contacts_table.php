<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToTeleCallerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            $table->text('note')->after('unlocked_date')->nullable();
        });
        Schema::table('lead_assigns', function (Blueprint $table) {
            $table->text('note')->after('unlocked_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            $table->dropColumn(['note']);
        });
        Schema::table('lead_assigns', function (Blueprint $table) {
            $table->dropColumn(['note']);
        });
    }
}
