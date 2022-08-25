<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSmsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->string('mobile_param')->nullable()->after('parameters');
            $table->string('msg_param')->nullable()->after('mobile_param');
            $table->integer('is_tested')->default(0)->comment('0 = not tested, 1 = tested');
            $table->integer('is_working')->default(0)->comment('0 = not working, 1 = working');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn('mobile_param');
            $table->dropColumn('msg_param');
            $table->dropColumn('is_tested');
            $table->dropColumn('is_working');
        });
    }
}
