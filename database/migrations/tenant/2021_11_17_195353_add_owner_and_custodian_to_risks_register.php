<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerAndCustodianToRisksRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('risks_register', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('status')->nullOnDelete();
            $table->unsignedBigInteger('custodian_id')->nullable()->after('status')->nullOnDelete();

            $table->foreign('owner_id')
                ->references('id')
                ->on('admins')
                ->nullOnDelete();
            $table->foreign('custodian_id')
                ->references('id')
                ->on('admins')
                ->nullOnDelete();
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
            $table->dropForeign('risks_register_owner_id_foreign');
            $table->dropColumn('owner_id');
            $table->dropForeign('risks_register_custodian_id_foreign');
            $table->dropColumn('custodian_id');
        });
    }
}
