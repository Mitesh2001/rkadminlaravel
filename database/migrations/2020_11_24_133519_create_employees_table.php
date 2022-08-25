<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->unsignedBigInteger('organization_id')->default(0);
			//$table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('cascade');
			$table->string('designation', 50);
			$table->enum('gender',["Male","Female"]);
			$table->string('mobile', 20);
			$table->string('mobile2', 20);
			$table->string('email');
			$table->unsignedBigInteger('dept');
			//$table->foreign('dept')->references('id')->on('departments')->onUpdate('cascade')->onDelete('cascade');
			$table->string('city');
			$table->text('address');
			$table->string('about');
			$table->date('date_birth')->nullable();
			$table->date('date_hire')->nullable();
			$table->date('date_left')->nullable();
			$table->softDeletes('deleted_at', 0);
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
        Schema::dropIfExists('employees');
    }
}
