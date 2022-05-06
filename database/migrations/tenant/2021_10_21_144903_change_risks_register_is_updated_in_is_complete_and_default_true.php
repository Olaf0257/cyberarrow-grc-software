<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRisksRegisterIsUpdatedInIsCompleteAndDefaultTrue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('risks_register', function (Blueprint $table) {
            $table->boolean('is_updated')->default(1)->change();
        });
        Schema::table('risks_register', function (Blueprint $table) {
            $table->renameColumn('is_updated', 'is_complete');
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
            $table->boolean('is_complete')->default(0)->change();
        });
        Schema::table('risks_register', function (Blueprint $table) {
            $table->renameColumn('is_complete', 'is_updated');
        });
    }
}
