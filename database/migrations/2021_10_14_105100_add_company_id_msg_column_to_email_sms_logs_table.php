<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdMsgColumnToEmailSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_sms_logs', function (Blueprint $table) {
            $table->bigInteger('company_id')->default(0)->nullable()->after('user_id');
            $table->text('msg')->nullable()->after('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_sms_logs', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('msg');
        });
    }
}
