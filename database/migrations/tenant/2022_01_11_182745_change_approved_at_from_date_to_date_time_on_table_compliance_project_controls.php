<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeApprovedAtFromDateToDateTimeOnTableComplianceProjectControls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compliance_project_controls', function (Blueprint $table) {
            $table->dateTime('approved_at')->nullable()->change();
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
            $table->date('approved_at')->nullable()->change();
        });
    }
}