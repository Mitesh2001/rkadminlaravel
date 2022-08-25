<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIdToContactValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_values', function (Blueprint $table) {
            $table->bigInteger('field_id')->after('company_id')->defualt(0);
            $table->index(['field_id']);
        });
        Schema::table('product_values', function (Blueprint $table) {
            $table->bigInteger('field_id')->after('company_id')->defualt(0);
            $table->index(['field_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_values', function (Blueprint $table) {
            $table->dropColumn(['field_id']);
        });
        Schema::table('product_values', function (Blueprint $table) {
            $table->dropColumn(['field_id']);
        });
    }
}
