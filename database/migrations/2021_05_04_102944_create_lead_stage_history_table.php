<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadStageHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_stage_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lead_id')->unsigned()->default(0);
            $table->tinyInteger('stage_id')->comment('1=cold,2=warm,3=hot,4=converted,5=closed,6=cros-sell/up-sell')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->index(['lead_id','stage_id','user_id']);
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
        Schema::dropIfExists('lead_stage_history');
    }
}
