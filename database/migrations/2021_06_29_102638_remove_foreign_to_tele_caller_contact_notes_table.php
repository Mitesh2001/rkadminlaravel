<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignToTeleCallerContactNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tele_caller_contact_notes', function (Blueprint $table) {
            DB::statement('ALTER TABLE tele_caller_contact_notes DROP FOREIGN KEY tele_caller_contact_notes_contact_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tele_caller_contact_notes', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('construction_contacts')->onDelete('cascade');
        });
    }
}
