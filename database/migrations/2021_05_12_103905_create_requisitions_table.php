<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->default(0);
            $table->bigInteger('company_id')->default(0);
            $table->tinyInteger('type')->comment('1=email,2=sms')->default(0);
            $table->bigInteger('quantity')->default(0);
            $table->tinyInteger('status')->comment('0=pending,1=accepte,2=reject')->default(0);
            $table->index(['client_id','company_id','type','quantity','status']);
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
        Schema::dropIfExists('requisitions');
    }
}
