<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->default(0)->after('client_id');
            $table->index(['company_id']);
        });
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->default(0)->after('client_id');
            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });

        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
    }
}
