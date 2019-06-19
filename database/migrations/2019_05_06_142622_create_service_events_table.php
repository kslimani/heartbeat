<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('service_status_id');
            $table->bigInteger('from_status_id');
            $table->bigInteger('to_status_id');
            $table->integer('elapsed')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('service_events');
    }
}
