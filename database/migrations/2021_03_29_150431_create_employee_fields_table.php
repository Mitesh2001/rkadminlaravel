<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('section_id')->unsigned();
            $table->foreign('section_id')->references('id')->on('employee_sections')->onDelete('cascade');
            $table->string('input_type')->nullable();
            $table->string('label_name')->nullable();
            $table->tinyInteger('is_required')->default(0);
            $table->tinyInteger('is_searchable')->default(0);
            $table->tinyInteger('is_select_multiple')->default(0);
            $table->integer('minlength')->nullable();
            $table->integer('maxlength')->nullable();
            $table->integer('minvalue')->nullable();
            $table->integer('maxvalue')->nullable();
            $table->string('pattern')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
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
        Schema::dropIfExists('employee_fields');
    }
}
