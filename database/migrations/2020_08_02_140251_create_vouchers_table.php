<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('card_no', 20);
            $table->decimal('value', 24, 2);
            $table->decimal('balance', 24, 2);

            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('companies');
            
            $table->timestamp('date')->nullable();
            $table->timestamp('expiry')->nullable();

            $table->integer('created_by')->unsigned()->nullable();

            // $table->timestamps();
        });

        Schema::create('voucher_topups', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('card_id')->unsigned();
            $table->foreign('card_id')->references('id')->on('vouchers');
            $table->decimal('amount', 24, 2);
            $table->integer('created_by')->unsigned()->nullable();

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
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('voucher_topups');
    }
}
