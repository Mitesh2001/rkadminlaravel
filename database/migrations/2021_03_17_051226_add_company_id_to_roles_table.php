<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->after('client_id');
            $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade');
        });
        Schema::table('permissions', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->after('client_id');
            $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade');
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
            DB::statement('ALTER TABLE roles DROP FOREIGN KEY roles_company_id_foreign'); 
            $table->dropColumn(['company_id']);
        });
        Schema::table('permissions', function (Blueprint $table) {
            DB::statement('ALTER TABLE permissions DROP FOREIGN KEY permissions_company_id_foreign');
            $table->dropColumn(['company_id']);
        });
    }
}
