<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('organization_id')->default(0);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobileno',20)->nullable();
            $table->string('alt_mobileno',20)->nullable();
            $table->string('designation')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
			$table->enum('gender',['M','F'])->nullable();
			$table->date('dob')->nullable();
			$table->string('picture')->nullable();
			$table->string('address_line_1')->nullable();
			$table->string('address_line_2')->nullable();
			$table->integer('country_id')->default(0);
			$table->integer('state_id')->default(0);
			$table->string('city')->nullable();
			$table->string('pincode',8)->nullable();
			$table->string('facebook')->nullable();
			$table->string('twitter')->nullable();
			$table->string('instagram')->nullable();
			$table->string('website')->nullable();
			$table->integer('noOfLoginAttempts')->unsigned()->nullable()->default(0);
            $table->integer('noOfLogins')->default(0);
			$table->tinyInteger('isLocked')->nullable()->default(0);
            $table->timestamp('lockedDate')->nullable();
			$table->text('token')->nullable();
			$table->text('forgetPasswordToken')->nullable();
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
        Schema::dropIfExists('users');
    }
}
