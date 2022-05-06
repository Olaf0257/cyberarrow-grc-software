<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskScoreMatrixLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_score_matrix_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('max_score')->nullable();
            $table->string('color');
            $table->bigInteger('level_type')->unsigned();
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
        Schema::dropIfExists('risk_score_matrix_levels');
    }
}
