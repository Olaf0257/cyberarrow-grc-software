<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceProjectControlCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_project_control_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_control_id')->unsigned();
            $table->foreign('project_control_id')->references('id')->on('compliance_project_controls')->onDelete('cascade');
            $table->bigInteger('from');
            $table->bigInteger('to');
            $table->text('comment');
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
        Schema::dropIfExists('compliance_project_control_comments');
    }
}
