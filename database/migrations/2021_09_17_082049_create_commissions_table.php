<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->float('commission_amt',10, 2)->default(0);
            $table->bigInteger('subscription_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('company_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('client_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('dealer_distributor')->unsigned()->nullable()->default(0);
            $table->string('is_payment_pending',10)->nullable();
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
        Schema::dropIfExists('commissions');
    }
}
