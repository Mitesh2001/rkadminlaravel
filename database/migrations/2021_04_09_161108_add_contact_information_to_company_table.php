<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactInformationToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->string('address_line_1')->after('company_logo')->nullable();
			$table->string('address_line_2')->after('address_line_1')->nullable();
			$table->string('city')->after('address_line_2')->nullable();
			$table->integer('state_id')->after('city')->unsigned()->default(0)->nullable();
			$table->integer('country_id')->after('state_id')->unsigned()->default(0)->nullable();
			$table->string('postcode')->after('country_id')->nullable();
            $table->tinyInteger('product_service')->default(0)->nullable()->after('postcode')->comment('1=product,2=service');
            $table->index(['state_id','country_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('company_contact_type')->default(0)->nullable()->after('password')->comment('1=primary,2=secondary,3=alternate');
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
            $table->dropColumn(['address_line_1','address_line_2','city','state_id','country_id','postcode','product_service']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_contact_type']);
        });
    }
}
