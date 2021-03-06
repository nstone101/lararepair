<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('type',20)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('module',20)->nullable();
            $table->string('module_id',5)->nullable();
            $table->string('to_address')->nullable();
            $table->string('from_address')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            
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
        Schema::dropIfExists('notification_logs');
    }
}
