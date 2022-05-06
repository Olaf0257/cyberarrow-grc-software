<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRisksRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risks_register', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->text('risk_description');
            $table->string('affected_properties');
            $table->string('affected_functions_or_assets')->default('All services and assets');

            $table->text('treatment');
            $table->enum('treatment_options', ['Mitigate', 'Accept', 'Closed']);
            $table->bigInteger('inherent_likelihood')->default(3);
            $table->bigInteger('inherent_impact')->default(2);
            $table->bigInteger('residual_likelihood')->default(3);
            $table->bigInteger('residual_impact')->default(2);
            $table->boolean('is_updated')->default(0);

            $table->foreign('category_id')
                    ->references('id')
                        ->on('risks_categories')->onDelete('cascade');

            $table->softDeletes();
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
        Schema::dropIfExists('risks_register');
    }
}
