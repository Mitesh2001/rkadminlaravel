<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeVariablesListToEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
			$table->string('external_key',25)->after('company_id')->nullable()->default(null);
            $table->tinyInteger('template_type')->after('default_template')->comment('1 from events, 2 for marketing')->nullable()->default(0);
            $table->text('template_variables')->after('template_type')->nullable()->default(null);
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
            $table->dropColumn(['external_key']);
            $table->dropColumn(['template_type']);
            $table->dropColumn(['template_variables']);
        });
    }
}
