<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientsPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('clients_plan', function (Blueprint $table) {
			$table->bigInteger('subscription_id')->after('company_id')->unsigned()->default(0);
			$table->float('plan_price',10, 2)->after('plan_id')->default(0);
			$table->float('discount',4, 2)->after('plan_price')->default(0);
			$table->float('discount_amount',10, 2)->after('discount')->default(0);
			$table->float('final_amount',10, 2)->after('discount_amount')->default(0);
			
			$table->index(['client_id']);
			$table->index(['company_id']);
			$table->index(['subscription_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients_plan', function (Blueprint $table) {
			$table->dropIndex(['client_id']);
			$table->dropIndex(['company_id']);
			$table->dropIndex(['subscription_id']);
            $table->dropColumn(['subscription_id','plan_price','discount','discount_amount','final_amount']);
        });
    }
}
