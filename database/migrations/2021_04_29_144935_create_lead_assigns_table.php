<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_assigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lead_id')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->tinyInteger('lock_status')->default(0)->comment('0=unlock,1=lock');
            $table->bigInteger('created_by')->default(0);
            $table->index(['lead_id','user_id','lock_status','created_by']);
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
        Schema::dropIfExists('lead_assigns');
    }
}
