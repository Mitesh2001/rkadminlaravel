<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferEndDateToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            DB::statement("ALTER TABLE `products` CHANGE `discount_percent` `discount_percent` DOUBLE(4,2) NOT NULL DEFAULT '0.00'");
            $table->date('offer_end_date')->after('document')->nullable();
            $table->string('unit')->after('offer_end_date')->nullable();
            $table->bigInteger('category_id')->after('unit')->default(0);
            $table->bigInteger('final_amount')->after('category_id')->default(0);
        });
        
        Schema::table('contact_sections', function (Blueprint $table) {
            $table->dropColumn(['contact_id']);
        });
        Schema::table('contact_fields', function (Blueprint $table) {
            $table->tinyInteger('is_pre_field')->after('section_id')->default(0)->comment('1=yes,2=no');
            $table->dropColumn(['contact_id']);
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
            $table->dropColumn(['offer_end_date','unit','category_id','final_amount']);
        });
        Schema::table('contact_sections', function (Blueprint $table) {
            $table->integer('contact_id')->default(0);
        });
        Schema::table('contact_fields', function (Blueprint $table) {
            $table->integer('contact_id')->default(0);
            $table->dropColumn(['is_pre_field']);
        });
    }
}
