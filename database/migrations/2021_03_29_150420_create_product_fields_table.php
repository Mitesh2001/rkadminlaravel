<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('section_id')->unsigned();
            $table->foreign('section_id')->references('id')->on('product_sections')->onDelete('cascade');
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
        Schema::dropIfExists('product_fields');
    }
}
