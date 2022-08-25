<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
			$table->string('skucode',20)->nullable();
			$table->string('name')->nullable();
			$table->text('description')->nullable();
			$table->text('comment')->nullable();
			$table->float('listprice',10, 2)->default(0);
			$table->float('discount_percent',3, 2)->default(0);
			$table->float('discount_amount',10, 2)->default(0);
			$table->float('tax1',3, 2)->default(0);
			$table->float('tax2',3, 2)->default(0);
			$table->float('tax3',3, 2)->default(0);
			$table->string('image')->nullable();
			$table->unsignedBigInteger('client_id')->default(0);
			$table->tinyInteger('product_type')->default(0)->comment('1 for product, 2 for service');
			$table->integer('created_by')->unsigned()->default(0);
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
			
			$table->index(['product_type']);
			$table->index(['client_id']);
			$table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
