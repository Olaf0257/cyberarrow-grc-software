<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyCampaignPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_campaign_policies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('policy_id')->comment('ID of the policy_policies table');
            $table->string('display_name');
            $table->enum('type', ['doculink', 'document']);
            $table->string('path');
            $table->string('version');
            $table->text('description');

            $table->foreign('campaign_id')
                ->references('id')
                    ->on('policy_campaigns')
                        ->onDelete('cascade');

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
        Schema::dropIfExists('policy_campaign_policies');
    }
}
