<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStateIdColumnTypeToClientsCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `clients` CHANGE `state_id` `state_id` VARCHAR(255) NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `company` CHANGE `state_id` `state_id` VARCHAR(255) NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `users` CHANGE `state_id` `state_id` VARCHAR(255) NULL DEFAULT NULL');
    }
}
