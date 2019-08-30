<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRtdToServiceStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_statuses', function (Blueprint $table) {
            $table->unsignedInteger('rtd')
                ->nullable()
                ->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_statuses', function (Blueprint $table) {
            $table->dropColumn('rtd');
        });
    }
}
