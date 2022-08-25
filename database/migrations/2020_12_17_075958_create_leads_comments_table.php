<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id')->unsigned();
            $table->integer('user_id')->unsigned();
			$table->text('remark');
			$table->softDeletes();
            $table->timestamps();
			$table->index(['lead_id']);
			$table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads_comments');
    }
}
