<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('date');
            $table->integer('sale_id')->nullable();
            $table->integer('repair_id')->nullable()->unsigned();
            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');
            
            $table->string('reference_no', 50)->nullable();
            $table->string('transaction_id', 50)->nullable();
            $table->string('paid_by', 20);
            $table->string('cheque_no', 20)->nullable();
            $table->string('cc_no', 20)->nullable();
            $table->string('cc_holder', 20)->nullable();
            $table->string('cc_month', 20)->nullable();
            $table->string('cc_year', 20)->nullable();
            $table->string('cc_type', 20)->nullable();
            $table->decimal('amount', 8, 2);
            $table->string('currency', 10)->nullable();

            $table->integer('created_by');
            $table->string('type', 20)->nullable();
            $table->text('note')->nullable();

            $table->decimal('pos_paid', 8, 2)->default(0);
            $table->decimal('pos_balance', 8, 2)->default(0);
            $table->string('approval_code', 20)->nullable();
            $table->string('cc_cvv', 20)->nullable();
            $table->boolean('approved')->default(0);

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
        Schema::dropIfExists('payments');
    }
}
