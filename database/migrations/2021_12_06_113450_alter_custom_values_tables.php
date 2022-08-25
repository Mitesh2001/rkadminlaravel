<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomValuesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_values', function (Blueprint $table) {
			$table->bigInteger('employee_id')->unsigned()->default(0)->after('company_id');
            $table->index(['employee_id']);
			$table->bigInteger('field_id')->after('employee_id')->defualt(0);
            $table->index(['field_id']);
		});
		Schema::table('product_values', function (Blueprint $table) {
			$table->bigInteger('product_id')->after('company_id')->unsigned()->default(0);
            $table->index(['product_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_values', function (Blueprint $table) {
			$table->dropColumn('employee_id');
            //$table->dropIndex(['employee_id']);
			$table->dropColumn('field_id');
            //$table->dropIndex(['field_id']);
		});
		Schema::table('product_values', function (Blueprint $table) {
			$table->dropColumn('product_id');
            //$table->dropIndex(['product_id']);
		});
    }
}
