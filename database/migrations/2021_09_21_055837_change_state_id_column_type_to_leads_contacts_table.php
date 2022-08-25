<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStateIdColumnTypeToLeadsContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `leads` CHANGE `state_id` `state_id` VARCHAR(255) NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `contacts` CHANGE `state_id` `state_id` VARCHAR(255) NULL DEFAULT NULL');
    }

}
