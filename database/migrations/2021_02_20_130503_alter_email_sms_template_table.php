<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmailSmsTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after("email_template_id")->default(0);
			$table->bigInteger('createdBy')->nullable()->default(0);
            $table->bigInteger('updatedBy')->nullable()->default(0);
			$table->softDeletes('deleted_at', 0);
			$table->index(['client_id']);
        });
		Schema::table('sms_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after("sms_template_id")->default(0);
			$table->bigInteger('createdBy')->nullable()->default(0);
            $table->bigInteger('updatedBy')->nullable()->default(0);
			$table->softDeletes('deleted_at', 0);
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
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('client_id');
            $table->dropColumn('createdBy');
            $table->dropColumn('updatedBy');
            $table->dropColumn('deleted_at');
			$table->dropIndex('client_id');
			$table->dropIndex('createdBy');
			$table->dropIndex('updatedBy');
        });
		
		Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn('client_id');
            $table->dropColumn('createdBy');
            $table->dropColumn('updatedBy');
            $table->dropColumn('deleted_at');
			$table->dropIndex('client_id');
			$table->dropIndex('createdBy');
			$table->dropIndex('updatedBy');
        });
    }
}
