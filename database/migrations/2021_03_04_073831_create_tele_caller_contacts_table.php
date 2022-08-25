<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeleCallerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tele_caller_contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('construction_contacts')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('is_working')->default(1)->comment('0=no,1=yes');
            $table->timestamps();
        });
        Schema::table('construction_contacts', function (Blueprint $table) {
            $table->tinyInteger('house_type')->after("budget")->comment('1=flat,2=office,3=house');
            $table->text('note')->after("contact_date")->nullable();
            $table->bigInteger('user_id')->unsigned()->after('contact_date')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tele_caller_contacts');
        if (Schema::hasColumn('construction_contacts', 'house_type'))
        {
            Schema::table('construction_contacts', function (Blueprint $table) {
                $table->dropColumn('house_type');
            });
        }
        if (Schema::hasColumn('construction_contacts', 'note'))
        {
            Schema::table('construction_contacts', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasColumn('construction_contacts', 'user_id'))
        {
            Schema::table('construction_contacts', function (Blueprint $table) {
                DB::statement('ALTER TABLE construction_contacts DROP FOREIGN KEY construction_contacts_user_id_foreign'); 
                $table->dropColumn('user_id');
            });
        }
        
    }
}
