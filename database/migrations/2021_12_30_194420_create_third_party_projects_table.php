<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('questionnaire_id');
            $table->unsignedBigInteger('vendor_id');
            $table->dateTime('launch_date');
            $table->dateTime('due_date');
            $table->string('timezone');
            $table->enum('frequency', ['Weekly', 'Biweekly', 'Monthly', 'Bi-anually', 'Annually']);
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->tinyInteger('score')->default(0);

            $table->foreign('vendor_id')->references('id')->on('third_party_vendors')->cascadeOnDelete();
            $table->foreign('questionnaire_id')->references('id')->on('third_party_questionnaires')->cascadeOnDelete();
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
        Schema::dropIfExists('third_party_projects');
    }
}
