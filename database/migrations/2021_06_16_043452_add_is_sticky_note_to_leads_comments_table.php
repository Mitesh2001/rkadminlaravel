<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsStickyNoteToLeadsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads_comments', function (Blueprint $table) {
            $table->tinyInteger('is_sticky_note')->default(0)->nullable()->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads_comments', function (Blueprint $table) {
            $table->dropColumn(['is_sticky_note']);
        });
    }
}
