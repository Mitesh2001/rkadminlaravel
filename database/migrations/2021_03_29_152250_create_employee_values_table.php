<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->unsigned()->default(0);
            $table->bigInteger('company_id')->unsigned()->default(0);
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->bigInteger('created_by')->unsigned()->default(0);
            $table->bigInteger('updated_by')->unsigned()->default(0);
            $table->bigInteger('deleted_by')->unsigned()->default(0);
            $table->index(['client_id','company_id',]);
            $table->index(['created_by','updated_by','deleted_by']);
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
        Schema::dropIfExists('employee_values');
    }
}
