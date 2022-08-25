<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstructionContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('construction_contacts', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('client_id')->default(0);
			$table->string('name')->nullable();
			$table->string('mobile_no', 20)->nullable();
			$table->string('business')->nullable();
			$table->string('cast')->nullable();
			$table->string('budget')->nullable();
			$table->string('flat_selection')->nullable();
			$table->string('fav_location')->nullable();
			$table->string('fav_floor')->nullable();
			$table->string('broker_name')->nullable();
			$table->string('broker_mobile_no')->nullable();			
			$table->string('reference')->nullable();
			$table->string('birthdates')->nullable();
			$table->string('anniversary')->nullable();
			$table->string('tokan_time')->nullable();
			$table->string('followup')->nullable();
			$table->string('remarks')->nullable();
			$table->string('il')->nullable();
			$table->date('contact_date')->nullable();			
			$table->integer('created_by')->unsigned()->default(0);
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
        Schema::dropIfExists('construction_contacts');
    }
}
