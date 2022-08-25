<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomFieldsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_fields', function (Blueprint $table) {
			$table->bigInteger('client_id')->after('id')->unsigned()->default(0);
            $table->bigInteger('company_id')->after('client_id')->unsigned()->default(0);
            $table->index('client_id');
            $table->index('company_id');
			$table->integer('input_type')->default(0)->comment('1=Text,2=Selectpicker,3=Radio,4=Checkbox,5=Textarea,6=Number,7=Datepicker')->change();
			$table->tinyInteger('is_pre_field')->after('section_id')->default(0)->comment('1=yes,2=no');
		});
		Schema::table('contact_fields', function (Blueprint $table) {
			$table->bigInteger('client_id')->after('id')->unsigned()->default(0);
            $table->bigInteger('company_id')->after('client_id')->unsigned()->default(0);
            $table->index('client_id');
            $table->index('company_id');
		});
		Schema::table('product_fields', function (Blueprint $table) {
			$table->bigInteger('client_id')->after('id')->unsigned()->default(0);
            $table->bigInteger('company_id')->after('client_id')->unsigned()->default(0);
            $table->index('client_id');
            $table->index('company_id');
			$table->tinyInteger('is_pre_field')->after('section_id')->default(0)->comment('1=yes,2=no')->change();
			$table->integer('input_type')->default(0)->comment('1=Text,2=Selectpicker,3=Radio,4=Checkbox,5=Textarea,6=Number,7=Datepicker')->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_fields', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
            $table->dropColumn(['is_pre_field']);
            $table->dropColumn(['client_id']);
			//$table->dropIndex(['client_id']);
			//$table->dropIndex(['company_id']);
			$table->string('input_type')->nullable()->change();
        });
		Schema::table('contact_fields', function (Blueprint $table) {
            $table->dropColumn(['client_id']);
            $table->dropColumn(['company_id']);
			//$table->dropIndex(['client_id']);
			//$table->dropIndex(['company_id']);
        });
		Schema::table('product_fields', function (Blueprint $table) {
            $table->dropColumn(['client_id']);
            $table->dropColumn(['company_id']);
			//$table->dropIndex(['client_id']);
			//$table->dropIndex(['company_id']);
            $table->dropColumn(['is_pre_field']);
			$table->string('input_type')->nullable()->change();
        });
    }
}
