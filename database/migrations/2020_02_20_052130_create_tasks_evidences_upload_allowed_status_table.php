<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksEvidencesUploadAllowedStatusTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tasks_evidences_upload_allowed_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_control_id');
            $table->boolean('status')->default(1);
            $table->foreign('project_control_id', 'evidences_upload_allow_project_control_id')->references('id')->on('compliance_project_controls')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tasks_evidences_upload_allowed_status');
    }
}
