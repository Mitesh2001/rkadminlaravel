	<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
			$table->string('name')->nullable();
			$table->string('email')->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('website')->nullable();
			$table->unsignedBigInteger('assigned_to')->default(0);
			//$table->foreign('assigned_to')->references('id')->on('users');
			$table->unsignedBigInteger('industry_id')->default(0);
			//$table->foreign('industry_id')->references('id')->on('industry_types');
			$table->date('connected_since')->nullable();
			$table->text('address')->nullable();
			$table->string('city')->nullable();
			$table->text('description')->nullable();
			$table->string('profile_image')->nullable();
			$table->string('profile')->nullable();
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
        Schema::dropIfExists('organizations');
    }
}
