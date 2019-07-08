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
            $table->unsignedBigInteger('service_status_id');
            $table->unsignedBigInteger('from_status_id');
            $table->unsignedBigInteger('to_status_id');
            $table->integer('elapsed')->nullable();
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
