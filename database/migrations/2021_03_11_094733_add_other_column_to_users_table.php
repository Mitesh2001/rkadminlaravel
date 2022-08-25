<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('construction_contacts', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->after('client_id');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->after('client_id');
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->after('client_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->after('client_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->after('organization_id');
            $table->bigInteger('cast_id')->unsigned()->nullable()->after('company_id');
            $table->foreign('cast_id')->references('id')->on('casts')->onDelete('cascade');
            $table->tinyInteger('marital_status')->comment('1=married,2=unmarried')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('critical_illness')->nullable();
            $table->string('legal_issue')->nullable();
            $table->string('other_activity')->nullable();
            $table->string('emergency_no')->nullable();
            $table->date('marriage_anniversary_date')->nullable();
            $table->string('driving_licence_no')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->longText('bank_details')->nullable();
            $table->longText('education_details')->nullable();
            $table->longText('family_details')->nullable();
            $table->longText('previous_employer_details')->nullable();
            $table->longText('job_details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE users DROP FOREIGN KEY users_cast_id_foreign');
            $table->dropColumn(['company_id','cast_id','marital_status','blood_group','critical_illness','legal_issue','other_activity','emergency_no','marriage_anniversary_date','driving_licence_no','aadhar_no','pan_no','bank_details','education_details','family_details','previous_employer_details','job_details']);
        });
        Schema::table('construction_contacts', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
