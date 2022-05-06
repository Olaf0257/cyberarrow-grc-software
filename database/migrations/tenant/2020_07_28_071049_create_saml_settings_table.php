<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamlSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saml_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sso_provider');
            $table->string('entity_id', 300);
            $table->string('sso_url', 500);
            $table->string('slo_url', 500);
            $table->longText('certificate');
            $table->boolean('is_x509certMulti')->default(0);
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
        Schema::dropIfExists('saml_settings');
    }
}
