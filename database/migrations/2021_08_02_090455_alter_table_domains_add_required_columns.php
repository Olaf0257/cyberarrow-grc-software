<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDomainsAddRequiredColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_primary')->default(false);
            $table->unsignedTinyInteger('is_fallback')->default(false);
            $table->string('certificate_status', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn('is_primary');
            $table->dropColumn('is_fallback');
            $table->dropColumn('certificate_status', 64);
        });
    }
}
