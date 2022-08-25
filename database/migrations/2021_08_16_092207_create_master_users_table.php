<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('m_type')->comment('1=admin user,2=normal user, 3=dealer, 4=distributor')->nullable();
            $table->bigInteger('m_user_id')->default(0)->nullable();
            $table->bigInteger('m_company_id')->default(0)->nullable();
            $table->bigInteger('m_client_id')->default(0)->nullable();
            $table->bigInteger('m_dealer_distributor_id')->default(0)->nullable();
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
        Schema::dropIfExists('master_users');
    }
}
