<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToNoticeBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notice_boards', function (Blueprint $table) {
            DB::statement('ALTER TABLE `notice_boards` CHANGE `end_date` `end_date_time` DATETIME NULL DEFAULT NULL');
            $table->bigInteger('company_id')->default(0)->nullable()->after('end_date_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notice_boards', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
    }
}
