<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('text');
            $table->unsignedBigInteger('questionnaire_id');
            $table->unsignedBigInteger('domain_id');
            $table->timestamps();

            $table->foreign('questionnaire_id')->references('id')->on('third_party_questionnaires')->onDelete('cascade');
            $table->foreign('domain_id')->references('id')->on('third_party_domains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_party_questions');
    }
}
