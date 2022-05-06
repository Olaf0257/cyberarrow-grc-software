<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksSubmitToApproverAllowedStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_submit_to_approver_allowed_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_control_id');
            $table->boolean('status')->default(0);
            $table->foreign('project_control_id', 'submit_to_approver_project_control_id')->references('id')->on('compliance_project_controls')->onDelete('cascade');
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
        Schema::dropIfExists('tasks_submit_to_approver_allowed_status');
    }
}
