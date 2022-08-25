<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompletedToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->tinyInteger('is_completed')->default(0)->comment('0=no,1=yes')->after('contact_id');
            DB::statement("ALTER TABLE `leads` CHANGE `user_id` `user_id` BIGINT(20) UNSIGNED NULL DEFAULT '0'");
        });

        Schema::create('lead_customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lead_id')->default(0)->nullable();
            $table->bigInteger('client_id')->default(0)->nullable();
            $table->bigInteger('company_id')->default(0)->nullable();
            $table->string('lead_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('secondary_mobile_no')->nullable();
            $table->year('established_in')->nullable();
            $table->string('turnover')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('no_of_employees')->nullable();
            $table->string('website')->nullable();
            $table->bigInteger('created_by')->default(0)->nullable();
            $table->index(['created_by','lead_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['is_completed']);
        });
        Schema::dropIfExists('lead_customers');
    }
}
