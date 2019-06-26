<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceStatusUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_status_user', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->bigInteger('service_status_id');
            $table->boolean('is_updatable')->default(false);
            $table->boolean('is_mute')->default(false);

            $table->unique(['user_id', 'service_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_status_user');
    }
}
