<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadIdToInterestedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interested_products', function (Blueprint $table) {
            $table->bigInteger('lead_id')->after('contact_id')->default(0);
            $table->index(['lead_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interested_products', function (Blueprint $table) {
            $table->dropColumn(['lead_id']);
        });
    }
}
