<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interested_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->default(0);
            $table->bigInteger('contact_id')->default(0);
            $table->index(['product_id','contact_id']);
            $table->bigInteger('created_by')->default(0);
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
        Schema::dropIfExists('interested_products');
    }
}
