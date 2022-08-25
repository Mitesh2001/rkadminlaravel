<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromEmailFromNameSenderIdToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->string('from_email')->nullable()->after('company_name');
            $table->string('from_name')->nullable()->after('from_email');
            $table->string('sms_sender_id')->nullable()->after('from_name');
            $table->enum('send_sms',["0","1"])->default(0)->nullable()->after('sms_sender_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('from_email');
            $table->dropColumn('from_name');
            $table->dropColumn('sms_sender_id');
            $table->dropColumn('send_sms');
        });
    }
}
