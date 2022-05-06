<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForColumnToComplianceProjectControlJustificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compliance_project_control_justifications', function (Blueprint $table) {
            $table->enum("for", ["rejected", "amend", "amend_reject"])->nullable()->after("justification");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compliance_project_control_justifications', function (Blueprint $table) {
            $table->dropColumn("for");
        });
    }
}