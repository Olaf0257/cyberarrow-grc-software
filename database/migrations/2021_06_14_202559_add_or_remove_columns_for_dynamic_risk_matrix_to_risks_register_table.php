<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrRemoveColumnsForDynamicRiskMatrixToRisksRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('risks_register', function (Blueprint $table) {
            $table->bigInteger('likelihood')->after('status');
            $table->bigInteger('impact')->after('likelihood');
            $table->bigInteger('inherent_score')->after('impact');
            $table->bigInteger('residual_score')->after('inherent_score');

            /* Deleting inherent_likelihood column*/
            if (Schema::hasColumn('risks_register', 'inherent_likelihood')){
                $table->dropColumn('inherent_likelihood');
            }

            /* Deleting inherent_impact column*/
            if (Schema::hasColumn('risks_register', 'inherent_impact')){
                $table->dropColumn('inherent_impact');
            }

            /* Deleting residual_likelihood column*/
            if (Schema::hasColumn('risks_register', 'residual_likelihood')){
                $table->dropColumn('residual_likelihood');
            }

            /* Deleting residual_impact column*/
            if (Schema::hasColumn('risks_register', 'residual_impact')){
                $table->dropColumn('residual_impact');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('risks_register', function (Blueprint $table) {
            /* Deleting likelihood column*/
            if (Schema::hasColumn('risks_register', 'likelihood')){
                $table->dropColumn('likelihood');
            }

            /* Deleting impact column */
            if (Schema::hasColumn('risks_register', 'impact')){
                $table->dropColumn('impact');
            }

            /* Deleting inherent_score column */
            if (Schema::hasColumn('risks_register', 'inherent_score')){
                $table->dropColumn('inherent_score');
            }

            /* Deleting residual_score column */
            if (Schema::hasColumn('risks_register', 'residual_score')){
                $table->dropColumn('residual_score');
            }
        });
    }
}
