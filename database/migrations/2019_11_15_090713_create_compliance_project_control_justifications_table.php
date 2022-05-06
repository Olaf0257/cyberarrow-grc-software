<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceProjectControlJustificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('compliance_project_control_justifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_control_id')->unsigned();
            $table->text('justification')->nullable();
            $table->bigInteger('creator_id')->unsigned();
            $table->foreign('project_control_id', 'pcontrol_id_foreign')->references('id')->on('compliance_project_controls')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('compliance_project_control_justifications');
    }
}
