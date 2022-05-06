<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRiskRegisterAffectedPropsAndAffectedFunctionsVarcharToTextAndString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('risks_register', function (Blueprint $table) {
            $table->string('affected_functions_or_assets', 255)->change();
            $table->text('affected_properties')->change();
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
            $table->string('affected_functions_or_assets')->change();;
            $table->string('affected_properties')->change();;
        });
    }
}
