<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextEvidenceToComplianceProjectControlEvidencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compliance_project_control_evidences', function (Blueprint $table) {
            $table->text('text_evidence')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compliance_project_control_evidences', function (Blueprint $table) {
            $table->dropColumn('text_evidence');
        });
    }
}
