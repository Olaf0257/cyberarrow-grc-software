<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRisksMappedComplianceControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risks_mapped_compliance_controls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('risk_id');
            $table->unsignedBigInteger('control_id');

            $table->foreign('risk_id')
                ->references('id')
                ->on('risks_register')->onDelete('cascade');
            $table->foreign('control_id')
                ->references('id')
                ->on('compliance_project_controls')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('risks_mapped_compliance_controls');
    }
}
