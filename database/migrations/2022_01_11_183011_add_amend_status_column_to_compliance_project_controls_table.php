<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmendStatusColumnToComplianceProjectControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compliance_project_controls', function (Blueprint $table) {
            $table->enum('amend_status', ['requested_approver', 'requested_responsible', 'accepted', 'rejected', 'submitted', 'solved',])->nullable()->after('frequency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compliance_project_controls', function (Blueprint $table) {
            $table->dropColumn("amend_status");
        });
    }
}