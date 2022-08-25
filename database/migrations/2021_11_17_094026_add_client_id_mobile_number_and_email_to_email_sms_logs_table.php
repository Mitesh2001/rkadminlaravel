<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdMobileNumberAndEmailToEmailSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_sms_logs', function (Blueprint $table) {
            $table->integer('client_id')->default(0)->after('log_id');
            $table->string('client_number')->nullable()->after('client_id');
            $table->string('client_email')->nullable()->after('client_number');
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
            $table->dropColumn('client_id');
            $table->dropColumn('client_number');
            $table->dropColumn('client_email');
        });
    }
}
