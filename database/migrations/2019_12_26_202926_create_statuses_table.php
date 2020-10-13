<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');

            $table->string('label')->nullable();
            $table->string('fg_color')->nullable();
            $table->string('bg_color')->nullable();
            $table->integer('position');
            $table->integer('send_sms');
            $table->integer('send_email');

            $table->longText('sms_text')->nullable();    
            $table->string('email_subject')->nullable();
            $table->longText('email_text')->nullable();   
            
            $table->integer('completed');
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
        Schema::dropIfExists('statuses');
    }
}
