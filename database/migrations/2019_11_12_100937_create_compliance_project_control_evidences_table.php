<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceProjectControlEvidencesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('compliance_project_control_evidences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_control_id')->unsigned();
            $table->string('name');
            $table->string('path', 500);
            $table->string('type');
            $table->date('deadline');
            $table->foreign('project_control_id')->references('id')->on('compliance_project_controls')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('compliance_project_control_evidences');
    }
}
