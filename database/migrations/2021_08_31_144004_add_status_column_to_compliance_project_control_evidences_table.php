<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToComplianceProjectControlEvidencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compliance_project_control_evidences', function (Blueprint $table) {
            $table->enum('status', ['initial', 'review', 'rejected', 'approved'])->nullable()->after('type');
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
            $table->dropColumn('status');
        });
    }
}
