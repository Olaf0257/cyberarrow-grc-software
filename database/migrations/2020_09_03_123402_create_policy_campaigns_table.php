<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status', ['active', 'archived']);
            $table->boolean('acknowledgement_email_sent')->default(0);
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->dateTime('launch_date');
            $table->dateTime('due_date')->nullable();
            $table->enum('auto_enroll_users', ['yes', 'no']);
            $table->string('timezone');

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
        Schema::dropIfExists('policy_campaigns');
    }
}
