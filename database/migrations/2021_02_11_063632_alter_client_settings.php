<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after("parent_id")->default(0);
			$table->index(['client_id']);
        });
		Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after("guard_name")->default(0);
			$table->index(['client_id']);
        });
		Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after("id")->default(0);
			$table->index(['client_id']);
        });
		Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after("id")->default(0);
            $table->string('enquiry_for')->after("sticky_note")->nullable();
            $table->string('reference')->after("enquiry_for")->nullable();
			$table->index(['client_id']);
        });
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('client_id');
			$table->dropIndex('client_id');
        });
		Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('client_id');
			$table->dropIndex('client_id');
        });
		Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('client_id');
			$table->dropIndex('client_id');
        });
		Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('client_id');
            $table->dropColumn('enquiry_for');
            $table->dropColumn('reference');
			$table->dropIndex('client_id');
        });
    }
}
