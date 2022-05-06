<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskScoreMatrixScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_score_matrix_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('score');
            $table->bigInteger('likelihood_index');
            $table->bigInteger('impact_index');
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
        Schema::dropIfExists('risk_score_matrix_scores');
    }
}
