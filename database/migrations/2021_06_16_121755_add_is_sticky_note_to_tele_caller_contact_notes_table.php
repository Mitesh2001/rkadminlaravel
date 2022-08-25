<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsStickyNoteToTeleCallerContactNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tele_caller_contact_notes', function (Blueprint $table) {
            $table->tinyInteger('is_sticky_note')->default(0)->nullable()->after('note');
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
            $table->dropColumn(['is_sticky_note']);
        });
    }
}
