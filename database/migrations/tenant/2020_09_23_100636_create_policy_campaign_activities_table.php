<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyCampaignActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_campaign_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id');
            $table->string('activity');
            $table->string('type');
            $table->unsignedBigInteger('user_id');

            $table->foreign('campaign_id')
                ->references('id')
                    ->on('policy_campaigns')
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
        Schema::dropIfExists('policy_campaign_activities');
    }
}
