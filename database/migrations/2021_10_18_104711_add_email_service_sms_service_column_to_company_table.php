<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailServiceSmsServiceColumnToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->enum('email_service',["0","1"])->default(0)->nullable()->after('product_service');
            $table->enum('sms_service',["0","1"])->default(0)->nullable()->after('email_service');
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
            $table->dropColumn('email_service');
            $table->dropColumn('sms_service');
        });
    }
}
