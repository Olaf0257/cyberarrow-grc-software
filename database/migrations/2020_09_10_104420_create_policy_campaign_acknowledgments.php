<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyCampaignAcknowledgments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_campaign_acknowledgments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('policy_id');
            $table->unsignedBigInteger('user_id');
            $table->text('token')->nullable();
            $table->enum('status', ['pending', 'completed']);

            $table->foreign('campaign_id')
                ->references('id')
                    ->on('policy_campaigns')
                        ->onDelete('cascade');
            $table->foreign('policy_id')
                ->references('id')
                    ->on('policy_campaign_policies')
                            ->onDelete('cascade');
            $table->foreign('user_id')
               ->references('id')
                    ->on('policy_campaign_group_users')
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
        Schema::dropIfExists('policy_campaign_acknowledgments');
    }
}
