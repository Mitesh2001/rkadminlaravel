<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreFieldToProductFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_fields', function (Blueprint $table) {
            $table->tinyInteger('is_pre_field')->after('section_id')->default(2)->comment('1=yes,2=no');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('cc_email')->nullable()->after('email');
            $table->string('bcc_email')->nullable()->after('cc_email');
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->string('cc_email')->nullable()->after('email');
            $table->string('bcc_email')->nullable()->after('cc_email');
        });
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            $table->bigInteger('created_by')->after('is_working')->default(0)->nullable();
            $table->bigInteger('unlocked_by')->after('created_by')->default(0)->nullable();
            $table->datetime('unlocked_date')->after('unlocked_by')->nullable();
            $table->datetime('locked_date')->after('created_by')->nullable();
        });
        Schema::table('lead_assigns', function (Blueprint $table) {
            $table->bigInteger('unlocked_by')->after('created_by')->default(0)->nullable();
            $table->datetime('locked_date')->after('created_by')->nullable();
            $table->datetime('unlocked_date')->after('unlocked_by')->nullable();
        });
        Schema::table('interested_products', function (Blueprint $table) {
            $table->bigInteger('deleted_by')->after('created_by')->default(0)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_fields', function (Blueprint $table) {
            $table->dropColumn(['is_pre_field']);
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['cc_email','bcc_email']);
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['cc_email','bcc_email']);
        });
        Schema::table('tele_caller_contacts', function (Blueprint $table) {
            $table->dropColumn(['created_by','unlocked_by','unlocked_date','locked_date']);
        });
        Schema::table('lead_assigns', function (Blueprint $table) {
            $table->dropColumn(['unlocked_by','locked_date','unlocked_date`']);
        });
        Schema::table('interested_products', function (Blueprint $table) {
            $table->dropColumn(['deleted_by','deleted_at']);
        });
    }
}
