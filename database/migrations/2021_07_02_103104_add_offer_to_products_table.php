<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['offer_end_date']);
            $table->datetime('offer_start_date_time')->nullable()->after('product_type');
            $table->datetime('offer_end_date_time')->nullable()->after('offer_start_date_time');
            $table->longText('offer_description')->nullable()->after('offer_end_date_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('offer_end_date')->nullable();
            $table->dropColumn(['offer_start_date_time','offer_end_date_time','offer_description']);
        });
    }
}
