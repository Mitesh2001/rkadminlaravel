<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectedColumnToQueryRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('query_rules', function (Blueprint $table) {
            $table->string('selected_column')->nullable()->after('rule_query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('query_rules', function (Blueprint $table) {
            $table->dropColumn(['selected_column']);
        });
    }
}
