<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name');
            $table->string('name')->nullable();
			$table->string('email')->nullable();
			$table->string('secondary_email')->nullable();
			$table->string('mobile_no', 20)->nullable();
			$table->string('secondary_mobile_no',20)->nullable();
            $table->year('established_in')->nullable();
			$table->string('turnover')->nullable();
			$table->string('gst_no',20)->nullable();
			$table->string('pan_no',20)->nullable();
			$table->string('no_of_employees',20)->nullable();
			$table->string('website')->nullable();
			$table->string('company_logo')->nullable();
			$table->string('picture')->nullable();
			$table->string('address_line_1')->nullable();
			$table->string('address_line_2')->nullable();
			$table->string('city')->nullable();
			$table->integer('state_id')->unsigned()->default(0);
			$table->integer('country_id')->unsigned()->default(0);
			$table->string('postcode',20)->nullable();
            $table->unsignedBigInteger('company_type_id')->default(0);
			$table->unsignedBigInteger('industry_id')->default(0);
			
            $table->bigInteger('client_number')->nullable();
			$table->integer('created_by')->unsigned()->default(0);
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
			
			$table->index(['company_type_id']);
			$table->index(['industry_id']);
			$table->index(['state_id']);
			$table->index(['country_id']);
			$table->index(['email']);
			$table->index(['mobile_no']);
			$table->index(['secondary_email']);
			$table->index(['secondary_mobile_no']);
			$table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
