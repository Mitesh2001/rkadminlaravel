<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignToTeleCallerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            $table->dropForeign('tele_caller_contacts_contact_id_foreign');
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
            $table->foreign('contact_id')->references('id')->on('construction_contacts')->onDelete('cascade');
        });
    }
}
