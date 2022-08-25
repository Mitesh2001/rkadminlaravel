<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('clients', function (Blueprint $table) {
        //     $table->dropColumn(['company_name','company_type_id','industry_id','gst_no','pan_no','no_of_employees','website','established_in','turnover']);
        // });
        Schema::table('clients', function (Blueprint $table) {
            DB::statement('ALTER TABLE `clients` CHANGE `company_name` `company_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        });

        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->unsigned();
            $table->string('company_name');
            $table->bigInteger('company_type_id')->unsigned()->nullable();
            $table->foreign('company_type_id')->references('id')->on('company_type')->onDelete('cascade');
            $table->bigInteger('industry_id')->unsigned()->nullable();
            $table->string('excise_no')->nullable();
            $table->string('vat_no')->nullable();
            $table->string('company_license_type')->nullable();
            $table->string('company_license_no')->nullable();
            $table->string('gst_no',20)->nullable();
			$table->string('pan_no',20)->nullable();
            $table->string('no_of_employees',20)->nullable();
            $table->string('website')->nullable();
            $table->year('established_in')->nullable();
            $table->string('turnover')->nullable();
			$table->string('company_logo')->nullable();
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
        Schema::dropIfExists('company');
    }
}
