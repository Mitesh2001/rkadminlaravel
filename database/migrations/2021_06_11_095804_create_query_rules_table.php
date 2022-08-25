<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->default(0)->nullable();
            $table->bigInteger('company_id')->default(0)->nullable();
            $table->bigInteger('user_id')->default(0)->nullable();
            $table->tinyInteger('module')->default(0)->nullable()->comment('1=lead,2=contact');
            $table->string('rule_query')->nullable();
            $table->string('rule_name')->nullable();
            $table->string('group_by')->nullable();
            $table->bigInteger('deleted_by')->default(0)->nullable();
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
        Schema::dropIfExists('query_rules');
    }
}
