<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactIdToContactFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_fields', function (Blueprint $table) {
            $table->bigInteger('contact_id')->unsigned()->default(0)->after('section_id');
            $table->index(['contact_id']);
        });
        Schema::table('contact_sections', function (Blueprint $table) {
            $table->bigInteger('contact_id')->unsigned()->default(0)->after('company_id');
            $table->index(['contact_id']);
        });
        Schema::table('contact_values', function (Blueprint $table) {
            $table->bigInteger('contact_id')->unsigned()->default(0)->after('company_id');
            $table->index(['contact_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_fields', function (Blueprint $table) {
            $table->dropColumn(['contact_id']);
        });
        Schema::table('contact_sections', function (Blueprint $table) {
            $table->dropColumn(['contact_id']);
        });
        Schema::table('contact_values', function (Blueprint $table) {
            $table->dropColumn(['contact_id']);
        });
    }
}
