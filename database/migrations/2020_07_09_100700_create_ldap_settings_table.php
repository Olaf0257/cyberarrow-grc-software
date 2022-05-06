<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdapSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ldap_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Mandatory Configuration Options
            $table->string('hosts');
            $table->string('base_dn');
            $table->string('username');
            $table->string('password');

            // Optional Configuration Options
            $table->string('port')->nullable();
            $table->string('use_ssl')->nullable();
            $table->string('version')->nullable();

            // Data Mapping
            $table->string('map_first_name_to');
            $table->string('map_last_name_to');
            $table->string('map_email_to');
            $table->string('map_contact_number_to')->nullable();
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
        Schema::dropIfExists('ldap_settings');
    }
}
