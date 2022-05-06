<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_vendors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contact_name');
            $table->string('email');
            $table->enum('status', ['active', 'disabled']);
            $table->string('country')->nullable();
            $table->tinyInteger('score')->default(0);
            $table->unsignedBigInteger('industry_id')->nullable();
            $table->timestamps();

            $table->foreign('industry_id')->references('id')->on('third_party_industries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_party_vendors');
    }
}
