<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceProjectControlsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('compliance_project_controls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_id')->unsigned();
            $table->string('name');
            $table->string('primary_id');
            $table->string('id_separator')->nullable();
            $table->string('sub_id');
            $table->text('description');
            $table->boolean('applicable')->default(1);
            $table->boolean('is_editable')->default(1);
            $table->bigInteger('current_cycle')->default(1);
            $table->string('status')->default('Not Implemented')->comment('Not Implemented, Under Review, Implemented, Rejected'); // Not Implemented, Under Review, Implemented, Rejected
            $table->bigInteger('responsible')->nullable();
            $table->bigInteger('approver')->nullable();
            $table->date('deadline')->nullable();
            $table->string('frequency')->nullable();
            $table->date('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->date('unlocked_at')->nullable();
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('compliance_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('compliance_project_controls');
    }
}
