<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\Admin\DefaultAdminSeeder;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('auth_method', ['Manual', 'LDAP']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('contact_number_country_code')->nullable();
            $table->string('contact_number')->nullable();
            $table->boolean('is_sso_auth')->default(0);
            $table->enum('status', ['unverified', 'active', 'disabled']);
            $table->dateTime('last_login')->nullable();
            $table->boolean('require_mfa')->default(0);
            $table->text('session_id')
                ->nullable()
                ->default(null)
                ->comment('Stores the id of the user session');
            $table->rememberToken();
            $table->timestamps();
        });

        $admin = new DefaultAdminSeeder();
        $admin->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
