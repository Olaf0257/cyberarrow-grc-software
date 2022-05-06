<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceStandardControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_standard_controls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('standard_id');
            $table->string('name');
            $table->string('slug');
            $table->string('primary_id');
            $table->string('sub_id');
            $table->string('id_separator')->nullable();
            $table->text('description');
            $table->timestamps();

            $table->foreign('standard_id')->references('id')->on('compliance_standards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compliance_standard_controls');
    }
}
