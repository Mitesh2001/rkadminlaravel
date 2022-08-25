<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('section_id')->unsigned();
            $table->foreign('section_id')->references('id')->on('contact_sections')->onDelete('cascade');
            $table->integer('input_type')->default(0)->comment('1=Text,2=Selectpicker,3=Radio,4=Checkbox,5=Textarea,6=Number,7=Datepicker');
            $table->string('label_name')->nullable();
            $table->tinyInteger('is_required')->default(0);
            $table->tinyInteger('is_searchable')->default(0);
            $table->tinyInteger('is_select_multiple')->default(0);
            $table->integer('minlength')->nullable();
            $table->integer('maxlength')->nullable();
            $table->integer('minvalue')->nullable();
            $table->integer('maxvalue')->nullable();
            $table->string('pattern')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('contact_fields');
    }
}
