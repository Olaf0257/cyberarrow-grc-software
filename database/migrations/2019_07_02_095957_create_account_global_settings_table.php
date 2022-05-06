<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('account_global_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name')->nullable();
            $table->string('primary_color');
            $table->string('secondary_color');
            $table->string('default_text_color');
            $table->string('timezone')->default('Asia/Dubai');
            $table->string('company_logo')->nullable();
            $table->string('small_company_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->boolean('allow_document_upload')->default(1);
            $table->boolean('allow_document_link')->default(1);
            $table->bigInteger('session_timeout')->nullable(); // minutes
            $table->string('secure_mfa_login')->nullable();
            $table->timestamps();
        });

        DB::table('account_global_settings')->insert([
            'display_name' => 'EBDAA GRC',
            'primary_color' => '#38414a',
            'secondary_color' => '#b2dd4c',
            'default_text_color' => '#fff',
            'company_logo' => 'assets/images/ebdaa-Logo.png',
            'favicon' => 'assets/images/ebdaa-Logo.png',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('account_global_settings');
    }
}
