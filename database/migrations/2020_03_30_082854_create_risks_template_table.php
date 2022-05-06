<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRisksTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risks_template', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('standard_id');
            $table->unsignedBigInteger('category_id');
            $table->string('primary_id')->nullable();
            $table->string('sub_id')->nullable();
            $table->string('name');
            $table->text('risk_description');
            $table->string('affected_properties');
            $table->text('treatment');

            $table->foreign('standard_id')
                    ->references('id')
                        ->on('risks_standards')->onDelete('cascade');
            $table->foreign('category_id')
                    ->references('id')
                        ->on('risks_categories')->onDelete('cascade');
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
        Schema::dropIfExists('risks_template');
    }
}
