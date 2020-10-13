<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_registers', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('date')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('closed_by')->nullable();

            $table->decimal('cash_in_hand', 24, 2)->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_cash', 24, 2)->nullable();
            $table->decimal('total_cheques', 24, 2)->nullable();
            $table->decimal('total_cc_slips', 24, 2)->nullable();

            $table->decimal('total_cash_submitted', 24, 2)->nullable();
            $table->decimal('total_cheques_submitted', 24, 2)->nullable();
            $table->decimal('total_cc_slips_submitted', 24, 2)->nullable();

            $table->string('note')->nullable();
            $table->string('transfer_opened_bills')->nullable();
            
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_registers');
    }
}
